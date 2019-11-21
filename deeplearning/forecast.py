import numpy as np
from bs4 import BeautifulSoup
from urllib.request import urlopen
from urllib import parse
import re
import requests
import urllib.request
import csv
from tensorflow.keras.models import Sequential
from tensorflow.keras.layers import Dense, Dropout
from tensorflow.keras import optimizers
from tensorflow.keras.utils import to_categorical
from tensorflow.keras.models import load_model
import pandas as pd
from datetime import datetime
import math
import time
from sklearn.preprocessing import StandardScaler


url="http://www.kma.go.kr/mobile/for_02_view.jsp?s1=36&s2=36110&s3=3611025000&q=&wday=0"
req=urlopen(url)
html=req.read()
soup=BeautifulSoup(html,'html.parser')
body=str(soup.body)

ho="시각"
te="기온"
di="풍향"

temper=0
tempIndex=0

PATH="/root/workspace/dust/deeplearning/"
for a in re.finditer(te,body):
    tempIndex=a.start()+5
    break


if body[tempIndex+1]>='0' and body[tempIndex+1]<='9':
    temper=int(body[tempIndex]+body[tempIndex+1])
else:
    temper=(int(body[tempIndex]))


hour=0
hourIndex=0

for a in re.finditer(ho,body):
    hourIndex=a.start()+5
    break
hour=int(body[hourIndex]+body[hourIndex+1])

direction=0
directionIndex=0

for a in re.finditer(di,body):
    directionIndex=a.start()+8
    break


if body[directionIndex]=='동':
    direction=1
if body[directionIndex]=='서':
    direction=2
if body[directionIndex]=='남':
    direction=3
if body[directionIndex]=='북':
    direction=4


ans=[hour,temper,direction]

fd=open(PATH+"data/result.csv","w",newline="")

csvwriter=csv.writer(fd)
csvwriter.writerow(ans)

fd.close()



result=np.loadtxt(PATH+"data/result.csv",delimiter=",",dtype=np.int64)

X_test=[result[1],result[2]]
input_array=np.array(X_test).reshape(1,2)
model=load_model(PATH+"model/pm1_0.h5")
pm1_0=model.predict(input_array)
model=load_model(PATH+"model/pm2_5.h5")
pm2_5=model.predict(input_array)
model=load_model(PATH+"model/pm10_0.h5")
pm10_0=model.predict(input_array)


ans=[]
real1_0=int(pm1_0)
real2_5=int(pm2_5)
real10_0=int(pm10_0)

ans.append(real1_0)
ans.append(real2_5)
ans.append(real10_0)
ans.append(result[0])
real=np.array(ans).reshape(4,1)

np.savetxt("/var/www/html/forecast/result.txt",real,fmt='%i',delimiter=',',newline=',')
