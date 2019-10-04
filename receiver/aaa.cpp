#include <iostream>
#include <fcntl.h>
#include <unistd.h>
#include <string>
#include <cstring>
using namespace std;


char xy[30];
int main(){

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
	cout<<ipfile<<endl;
	
	char getLocation[100]="curl ipinfo.io/";
	char curl[256];
	sprintf(curl,"%s%s > /dev/null 2>&1 > location.txt",getLocation,ipfile);
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

	return 0;
}
