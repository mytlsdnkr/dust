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


	PGconn *conn=PQconnectdb("user=root dbname=dust");

	if (PQstatus(conn) == CONNECTION_BAD) {

		fprintf(stderr, "Connection to database failed: %s\n",
				PQerrorMessage(conn));
		exit(1);
	}


	while(1){
		//파일 오픈 및 파이프 오픈
		fp=popen("sudo python3 /home/park/workspace/dust/PMS7003/sender.py","r");
		fd=open(csvPath,O_WRONLY|O_CREAT|O_APPEND,0777);
		//오픈한 파이프로 데이터 받기
		fgets(buff,32,fp);
		if(buff[0]<'0' || buff[0]>'9'){
			memset(buff,'\0',sizeof(buff));
			close(fd);
			pclose(fp);
			continue;
		}
		//파이프를 통해 받은 파일을 csv형태로 변환
		//csv파일에 내용 담기
		write(fd,buff,strlen(buff));
		//데이터베이스에 담기 위한 strtok 과정
		result=strtok(buff,",");
		while(result!=NULL){
			dust[i++]=atoi(result);
			result=strtok(NULL,",");
		}
		i=0;
		sprintf(query,"insert into dust(timestamp,pm1_0,pm2_5,pm10_0) values(%d,%d,%d,%d)",dust[0],dust[1],dust[2],dust[3]);
		res=PQexec(conn,query);
		if(PQresultStatus(res)!=PGRES_COMMAND_OK){
			fprintf(stderr,"inser command failed:%s",PQerrorMessage(conn));
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
