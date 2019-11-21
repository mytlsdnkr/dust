from bs4 import BeautifulSoup
from urllib.request import urlopen
from urllib.parse import quote
from time import sleep
import requests
import urllib.request
 
from scrapy.selector import Selector

def img_url_from_page(url):
    html = requests.get(url).text  # r = requests.get(url); html = r.text
    sel = Selector(text=html)
    img_names = sel.css('td a img::attr(src)').extract()
    img_names = [img_name for img_name in img_names]
    return img_names
 
 
def img_from_url(image_names,count):
    name = count
    full_name = "/var/www/html/images/" + str(name) + ".jpg"
    urllib.request.urlretrieve(image_names, full_name)


url="http://browse.auction.co.kr/search?keyword=%eb%af%b8%ec%84%b8%eb%a8%bc%ec%a7%80&itemno=&nickname=&frm=hometab&dom=auction&isSuggestion=No&retry=&Fwk=%eb%af%b8%ec%84%b8%eb%a8%bc%ec%a7%80&acode=SRP_SU_0100&arraycategory=&encKeyword=%eb%af%b8%ec%84%b8%eb%a8%bc%ec%a7%80&s=8"

req=urlopen(url)
html=req.read()
soup=BeautifulSoup(html,'html.parser')

price=soup.find_all('strong','text--price_seller')
title=soup.find_all('span','text--title')
    
count=1
for i in title:
    if count==11:
        break
    inputSearch=i.get_text()
    base_url = "https://www.google.co.kr/search?biw=1597&bih=925&" \
             "tbm=isch&sa=1&btnG=%EA%B2%80%EC%83%89&q=" + inputSearch

    for k in img_url_from_page(base_url):
        img_from_url(k,count)
        break
    count=count+1
    sleep(5)

    
addinfo=soup.find_all('span','text--addinfo')
review=soup.find_all('li',"item awards")
reviewCount=soup.find_all('span','text--reviewcnt')
sellCount=soup.find_all('span','text--buycnt')

ref=soup.find_all('a','link--itemcard')
refArray=[]
count=0
for i in ref:
    if count%2==0:
        refArray.append(i['href'])

    count=count+1


w,h=7,10


Matrix=[[0 for x in range(w)] for y in range(h)]

for i in range(0,10):
        Matrix[i][0]=price[i].get_text()
        Matrix[i][1]=title[i].get_text()
        Matrix[i][2]=addinfo[i].get_text()
        Matrix[i][3]=review[i].get_text()
        Matrix[i][4]=reviewCount[i].get_text()
        Matrix[i][5]=sellCount[i].get_text()
        Matrix[i][6]=refArray[i]


    


for i in range(0,10):
    for j in range(0,7):
        if j==7:
            break
        print(Matrix[i][j],end='#')
    print()
    
 





