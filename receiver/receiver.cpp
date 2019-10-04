#include <iostream>
#include <string>
#include "receiver.h"
#include "HTTPRequest.hpp"
using namespace std;
char xy[30];
void getLocation(){

	system("curl -XGET http://checkip.dyndns.org/ > /dev/null 2>&1 > ip.txt");
	int fd;
	char ipfile[128];
	fd=open("ip.txt",O_RDONLY);
	read(fd,ipfile,128);
	close(fd);
	ipfile[strlen(ipfile)-1]='\0';
	string ip(ipfile);

	ipfile[0]='\0';
	int k=0;

	for(char ch : ip){
		if((ch>='0' && ch<='9') || ch=='.'){
			ipfile[k++]=ch;
		}
	}

	ipfile[k]='\0';
	
	char getLocation[100]="curl ipinfo.io/";
	char curl[256];
	sprintf(curl,"%s%s?=token=c704fc03805be2 > /dev/null 2>&1 > location.txt",getLocation,ipfile);
	system(curl);
	fd=open("location.txt",O_RDONLY);
	char location[256];
	read(fd,location,256);

	string realLocation(location);
	string::size_type n;

	n=realLocation.find("\"loc\"");
	n+=7;
	int i=n+1;
	k=0;
	for(i;;i++){
		if(realLocation[i]=='\"'){
			break;
		}
		xy[k++]=realLocation[i];
	}


}

void getValuebyAPI(PGconn *conn){
	int i;
	PGresult *res;
	http::Request request("http://openapi.airkorea.or.kr/openapi/services/rest/ArpltnInforInqireSvc/getMsrstnAcctoRltmMesureDnsty?stationName=%EC%8B%A0%ED%9D%A5%EB%8F%99&dataTerm=month&pageNo=1&numOfRows=1&ServiceKey=xkP3%2BU6byPJY0GytOiIhrF0XgIb35B%2BT54uazeU0ITME0HCsJkzo1cdCgp1ipWAUExRtVFgxNhvrj0WjtCKgiw%3D%3D&ver=1.3");
	http::Response response=request.send("GET");

	string r=string(response.body.begin(),response.body.end());
	string::size_type n;
	//get time
	int len=r.length();
	char cyear[12];
	char cmonth[12];
	char cday[12];
	char chour[12];
	char cmin[12];
	int k=0;

	int apifd;
	apifd=open("/home/park/workspace/dust/csv/api.csv",O_WRONLY|O_CREAT|O_APPEND,0777);

	n=r.find("<dataTime>");
	i=n+10;
	for(i;i<len;i++){
		if(r[i]=='-')
			break;
		cyear[k++]=r[i];
	}
	cyear[k]='\0';
	i++;

	k=0;

	for(i;i<len;i++){
		if(r[i]=='-')
			break;
		cmonth[k++]=r[i];
	}
	cmonth[k]='\0';
	i++;
	k=0;


	for(i;i<len;i++){
		if(r[i]==' ')
			break;
		cday[k++]=r[i];
	}
	i++;
	cday[k]='\0';
	k=0;
	for(i;i<len;i++){
		if(r[i]==':')
			break;
		chour[k++]=r[i];
	}
	i++;
	chour[k]='\0';
	k=0;
	for(i;i<len;i++){
		if(r[i]=='<')
			break;
		cmin[k++]=r[i];
	}

	cmin[k]='\0';

	k=0;
	int year=atoi(cyear);
	int month=atoi(cmonth);
	int day=atoi(cday);
	int hour=atoi(chour);
	int min=atoi(cmin);


	struct tm a;

	a.tm_year=year-1900;
	a.tm_mon=month-1;
	a.tm_mday=day;
	a.tm_hour=hour;
	a.tm_min=min;
	a.tm_sec=0;
	time_t lastTime=mktime(&a);

	//get pm1.0
	n=r.find("<pm10Value>");
	i=n+11;
	char getPM10[16];
	for(i;i<len;i++){
		if(r[i]=='<')
			break;
		getPM10[k++]=r[i];
	}
	getPM10[k]='\0';
	k=0;

	//get pm2.5

	n=r.find("<pm25Value>");
	i=n+11;
	char getPM2_5[16];
	for(i;i<len;i++){
		if(r[i]=='<')
			break;
		getPM2_5[k++]=r[i];
	}
	getPM2_5[k]='\0';
	k=0;
	int unixTime=(int)lastTime;
	char goCSV[64];
	sprintf(goCSV,"%d,%s,%s\n",unixTime,getPM10,getPM2_5);
	write(apifd,goCSV,strlen(goCSV));
	close(apifd);
	int PM10=atoi(getPM10);
	int PM2_5=atoi(getPM2_5);
	if(getPM10[0]=='-'){
		PM10=-1;
	}

	if(getPM2_5[0]=='-'){
		PM2_5=-1;
	}

	char query[256];
	sprintf(query,"insert into api(timestamp,pm10,pm2_5) values(%d,%d,%d)",unixTime,PM10,PM2_5);
	res=PQexec(conn,query);
	if(PQresultStatus(res)!=PGRES_COMMAND_OK){
		fprintf(stderr,"insert command failed:%s",PQerrorMessage(conn));
	}
	PQclear(res);
}

int main(){
	FILE *fp;
	char buff[32];
	char *result;
	char query[128];
	PGresult *res;
	int dust[4];
	int i=0;
	int fd;

	time_t last_time;
	last_time=time(NULL);
	PGconn *conn=PQconnectdb("user=root dbname=dust");

	if (PQstatus(conn) == CONNECTION_BAD) {

		fprintf(stderr, "Connection to database failed: %s\n",
				PQerrorMessage(conn));
		exit(1);
	}
	getValuebyAPI(conn);
			getLocation();

	while(1){
		time_t current_time=time(NULL);
		if(current_time>=(last_time+3600)){
			last_time=current_time;
			getLocation();
			getValuebyAPI(conn);
		}
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
		buff[strlen(buff)-1]='\0';
		char re[256];
		//파이프를 통해 받은 파일을 csv형태로 변환
		//csv파일에 내용 담기
		sprintf(re,"%s,%s\n",buff,xy);
		write(fd,re,strlen(re));
		//write(fd,buff,strlen(buff));
		//데이터베이스에 담기 위한 strtok 과정
		result=strtok(buff,",");
		while(result!=NULL){
			dust[i++]=atoi(result);
			result=strtok(NULL,",");
		}
		i=0;
		
		sprintf(query,"insert into dust(timestamp,pm1_0,pm2_5,pm10_0,location) values(%d,%d,%d,%d,'%s')",dust[0],dust[1],dust[2],dust[3],xy);
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
