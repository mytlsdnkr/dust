#include "receiver.h"


int main(){

    FILE *fp;
    char buff[32];
    char *result;
    char toCsv[32];
    char csvpath[64];
    char date[16];
    char query[128];
    PGresult *res;
    int dust[4];
    int i=0;
    int fd;
    time_t t;
    struct tm *tm;
    

    PGconn *conn=PQconnectdb("user=park dbname=dust");

    if (PQstatus(conn) == CONNECTION_BAD) {

        fprintf(stderr, "Connection to database failed: %s\n",
                PQerrorMessage(conn));
        exit(1);
    }


    while(1){
        //오늘 날짜 구하기
        t=time(NULL);
        tm=localtime(&t);
        //날짜를 파일이름으로 저장
        sprintf(date,"%d-%d-%d.csv",tm->tm_year+1900,tm->tm_mon+1,tm->tm_mday);
        sprintf(csvpath,"%s%s",csvPath,date);
        //파일 오픈 및 파이프 오픈
        fd=open(csvpath,O_WRONLY|O_CREAT|O_APPEND,0777);
        fp=popen("sudo python3 /home/park/workspace/dust/PMS7003/sender.py","r");
        if(fp==NULL){
            perror("popen()실패");
            return -1;
        }
        //오픈한 파이프로 데이터 받기
        fgets(buff,32,fp);
        if(buff[0]<'0' || buff[0]>'9'){
            memset(buff,'\0',sizeof(buff));
            pclose(fp);
            continue;
        }
        printf("%s\n",buff);
            
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
        PQclear(res);
        pclose(fp);
        memset(dust,0,sizeof(dust));
        memset(buff,'\0',sizeof(buff));
        sleep(58);
        

    }

    PQfinish(conn);
   




    return 0;
}
