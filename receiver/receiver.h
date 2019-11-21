#include <iostream>
#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <netinet/in.h>
#include <string.h>
#include <time.h>
#include <fcntl.h>
#include <string>
#include "HTTPRequest.hpp"
#include "/usr/include/postgresql/libpq-fe.h"
using namespace std;

#define SIZE 512
const char *senderPath="sudo python3 /root/workspace/dust/PMS7003/sender.py";
const char *csvPath="/root/workspace/dust/csv/dust.csv";
const char *crawlingPath="/root/workspace/csv/crawling.csv";


//IP주소를 이용한 현재 위치 파악  -- > 사용X
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


//API정보를 이용한 외부 미세먼지 데이터 수집
void getValuebyAPI(PGconn *conn, int temperature, int direction){
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
	apifd=open("/root/workspace/dust/csv/api.csv",O_WRONLY|O_CREAT|O_APPEND,0777);

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
	sprintf(goCSV,"%d,%s,%s,%d,%d\n",unixTime,getPM10,getPM2_5,temperature,direction);
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


//옥션 미세먼지 제품 판매 인기순 크롤링
void crawling(PGconn *conn){
	PGresult *res;
	FILE *fp;
	char buff[SIZE];
	char *result;
	char query[SIZE];
	char price[SIZE];
	char title[SIZE];
	char addinfo[SIZE];
	char reviewscore[SIZE];
	char reviewcount[SIZE];
	char sellcount[SIZE];
	char reference[SIZE];
	int i=0;
	int fd;
	int k=0;

	sprintf(query,"delete from product");
	res=PQexec(conn,query);
	if(PQresultStatus(res)!=PGRES_COMMAND_OK){
		fprintf(stderr,"delete command failed:%s",PQerrorMessage(conn));
	}
	PQclear(res);
	fp=popen("sudo python3 /root/workspace/dust/crawling/auction.py","r");

	while(fgets(buff,SIZE,fp)!=NULL){
		k=0;
		result=strtok(buff,"#");
		while(result!=NULL){
			switch(k){
				case 0:
					strcpy(title,result);
					break;
				case 1:
					strcpy(price,result);
					break;
				case 2:
					strcpy(addinfo,result);
					break;
				case 3:
					strcpy(reviewscore,result);
					break;
				case 4:
					strcpy(reviewcount,result);
					break;
				case 5:
					strcpy(sellcount,result);
					break;
				case 6:
					strcpy(reference,result);
					break;
				default:
					break;
			}
			result=strtok(NULL,"#");
			k++;
		}
		sprintf(query,"insert into product(title,price,addinfo,reviewscore,reviewcount,sellcount,reference) values('%s','%s','%s','%s','%s','%s','%s')",price,title,addinfo,reviewscore,reviewcount,sellcount,reference);
		res=PQexec(conn,query);
		if(PQresultStatus(res)!=PGRES_COMMAND_OK){
			fprintf(stderr,"insert command failed:%s\n%s\n",PQerrorMessage(conn),query);
		}
		PQclear(res);
		memset(price,'\0',sizeof(price));
		memset(title,'\0',sizeof(title));
		memset(addinfo,'\0',sizeof(addinfo));
		memset(reviewscore,'\0',sizeof(reviewscore));
		memset(reviewcount,'\0',sizeof(reviewcount));
		memset(sellcount,'\0',sizeof(sellcount));
		memset(reference,'\0',sizeof(reference));
	}
}

//현재 외부 온도 얻기
int getTemp(){
	FILE *fp;
	char temp[3];
	fp=popen("sudo python3 /root/workspace/dust/crawling/todaytemp.py","r");
	fgets(temp,3,fp);
	fclose(fp);
	return atoi(temp);
}
//현재 풍향 얻기
int getDirection(){
	FILE *fp;
	char temp[3];
	fp=popen("sudo python3 /root/workspace/dust/crawling/direction.py","r");
	fgets(temp,3,fp);
	fclose(fp);
	return atoi(temp);


}


