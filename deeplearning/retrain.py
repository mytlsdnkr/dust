import numpy as np
import pandas as pd
from datetime import datetime
import time
import os
from tensorflow.keras.models import Sequential
from tensorflow.keras.layers import Dense, Dropout
from tensorflow.keras import optimizers
from tensorflow.keras.utils import to_categorical
from tensorflow.keras.models import load_model
from tensorflow.keras.callbacks import EarlyStopping
from sklearn.preprocessing import StandardScaler,RobustScaler,MinMaxScaler,MaxAbsScaler


# 데이터 분리
data=pd.read_csv("/root/workspace/dust/csv/dust.csv",header=None,names=['timestamp','pm1.0','pm2.5','pm10.0','temp','direction']);
TESTSIZE=10000
data=data[data['pm10.0']!=0]


PATH="/root/workspace/dust/deeplearning/"

matrix=[[0 for col in range(5)] for row in range(TESTSIZE)]
for i in range(0,TESTSIZE):
    matrix[i][0]=data['pm1.0'][i]
    matrix[i][1]=data['pm2.5'][i]
    matrix[i][2]=data['pm10.0'][i]
    matrix[i][3]=data['temp'][i]
    matrix[i][4]=data['direction'][i]


X=np.array(matrix)
np.savetxt(PATH+"data/data.csv",X,fmt="%i",delimiter=",")



data.to_csv("/root/workspace/dust/csv/dust.csv",mode="w",header=False,index=False)


#데이터 학습용으로 분리
data=pd.read_csv(PATH+"data/data.csv",header=None,names=['pm1.0','pm2.5','pm10.0','temp','direction']);

TESTSIZE=len(data)
timeArray=[]
temperature=[]
count=0

variable=[[0 for col in range(2)] for row in range(TESTSIZE)]
for i in range(0,TESTSIZE):
    variable[i][0]=data['temp'][i]
    variable[i][1]=data['direction'][i]


X=np.array(variable)
#Get Y

PM1_0=[[0 for col in range(1)] for row in range(TESTSIZE)]

for i in range(0,TESTSIZE):
    PM1_0[i][0]=int(data['pm1.0'][i])


PM2_5=[[0 for col in range(1)] for row in range(TESTSIZE)]

for i in range(0,TESTSIZE):
    PM2_5[i][0]=int(data['pm2.5'][i])



PM10=[[0 for col in range(1)] for row in range(TESTSIZE)]

for i in range(0,TESTSIZE):
    PM10[i][0]=int(data['pm10.0'][i])


PM1_0Data=np.array(PM1_0)
PM2_5Data=np.array(PM2_5)
PM10Data=np.array(PM10)

np.savetxt(PATH+"data/X.csv",X,fmt="%i",delimiter=",")

np.savetxt(PATH+"data/pm1_0.csv",PM1_0Data,fmt="%i",delimiter=",")
np.savetxt(PATH+"data/pm2_5.csv",PM2_5Data,fmt="%i",delimiter=",")
np.savetxt(PATH+"data/pm10_0.csv",PM10Data,fmt="%i",delimiter=",")

#데이터 불러온 후 retrain
x=np.loadtxt(PATH+"data/X.csv",delimiter=",",dtype=np.int64)
pm1_0data=np.loadtxt(PATH+"data/pm1_0.csv",delimiter=",",dtype=np.int64)
pm2_5data=np.loadtxt(PATH+"data/pm2_5.csv",delimiter=",",dtype=np.int64)
pm10_0data=np.loadtxt(PATH+"data/pm10_0.csv",delimiter=",",dtype=np.int64)

model1_0=load_model(PATH+"model/pm1_0.h5")
model2_5=load_model(PATH+"model/pm2_5.h5")
model10_0=load_model(PATH+"model/pm10_0.h5")

model1_0.fit(x,pm1_0data,batch_size=1,epochs=30,shuffle=False)
model1_0.save(PATH+"model/pm1_0.h5")

model2_5.fit(x,pm2_5data,batch_size=1,epochs=30,shuffle=False)
model2_5.save(PATH+"model/pm2_5.h5")

model10_0.fit(x,pm10_0data,batch_size=1,epochs=30,shuffle=False)
model10_0.save(PATH+"model/pm10_0.h5")

