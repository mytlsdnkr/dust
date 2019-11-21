#include "receiver.h"
int main(){
	FILE *fp;
	char buff[32];
	char *result;
	char query[128];
	PGresult *res;
	int dust[4];
	int i=0;
	int fd;
	int temperature=0;
	int direction=0;
	struct tm *t;

	time_t last_time;
	last_time=time(NULL);
	PGconn *conn=PQconnectdb("user=root dbname=dust");

	if (PQstatus(conn) == CONNECTION_BAD) {

		fprintf(stderr, "Connection to database failed: %s\n",
				PQerrorMessage(conn));
		exit(1);
	}

	temperature=getTemp();  // 현재 온도 파악
	direction=getDirection(); // 현재 풍향 파악
	getValuebyAPI(conn,temperature,direction); //API로 미세먼지 수치 가져오기
	crawling(conn); // 미세먼지 제품 크롤링

	while(1){

		time_t current_time=time(NULL);
		t=localtime(&current_time);
		// 매일 00시에 데이터 10000개로 모델 학습
		if(t->tm_hour==0 && t->tm_min<1){
			system("sudo python3 /root/workspace/dust/deeplearning/retrain.py");
		}
		//1시간 간격으로 예측, 온도, 풍향, API 수치, 제품 크롤링 
		if(current_time>=(last_time+3600)){
			system("sudo python3 /root/workspace/dust/deeplearning/forecast.py");
			temperature=getTemp();
			direction=getDirection();
			getValuebyAPI(conn,temperature,direction);
			crawling(conn);
			last_time=current_time;
		}

		//실시간으로 내부 미세먼지 수치 측정
		//파일 오픈 및 파이프 오픈
		fp=popen("sudo python3 /root/workspace/dust/PMS7003/sender.py","r");
		fd=open(csvPath,O_WRONLY|O_CREAT|O_APPEND,0777);
		//오픈한 파이프로 데이터 받기
		fgets(buff,32,fp);
		if(buff[0]<'0' || buff[0]>'9'){
			memset(buff,'\0',sizeof(buff));
			close(fd);
			pclose(fp);
			continue;
		}
		char re[256];
		buff[strlen(buff)-1]='\0';
		//파이프를 통해 받은 파일을 csv형태로 변환
		//csv파일에 내용 담기
		sprintf(re,"%s,%d,%d\n",buff,temperature,direction);
		write(fd,re,strlen(re));
		//데이터베이스에 담기 위한 strtok 과정
		result=strtok(re,",");
		while(result!=NULL){
			dust[i++]=atoi(result);
			result=strtok(NULL,",");
		}
		i=0;

		sprintf(query,"insert into dust(timestamp,pm1_0,pm2_5,pm10_0) values(%d,%d,%d,%d)",dust[0],dust[1],dust[2],dust[3]);
		res=PQexec(conn,query);
		if(PQresultStatus(res)!=PGRES_COMMAND_OK){
			fprintf(stderr,"insert command failed:%s",PQerrorMessage(conn));
		}
		PQclear(res);
		memset(dust,0,sizeof(dust));
		memset(buff,'\0',sizeof(buff));
		pclose(fp);
		close(fd);

	}

	PQfinish(conn);
	return 0;
}
