
import numpy as np
from tensorflow.keras.models import Sequential
from tensorflow.keras.layers import Dense, Dropout
from tensorflow.keras import optimizers
from tensorflow.keras.utils import to_categorical
from tensorflow.keras.models import load_model
from tensorflow.keras.callbacks import EarlyStopping
import pandas as pd
from datetime import datetime
import time
from sklearn.preprocessing import StandardScaler,RobustScaler,MinMaxScaler,MaxAbsScaler
import matplotlib.pyplot as plt
x=np.loadtxt("X.csv",delimiter=",",dtype=np.int64)
y=np.loadtxt("pm10_0.csv",delimiter=",",dtype=np.int64)

'''Ytemp=y.reshape(len(y),-1)
standardScaler=StandardScaler()
standardScaler.fit(x)
standardScaler.fit(Ytemp)
X=standardScaler.transform(x)
Y=standardScaler.transform(Ytemp)
'''
''' PM1_0
model.add(Dense(20,input_dim=2,activation='relu'))
Dropout(0.2)
model.add(Dense(20,activation='relu'))
Dropout(0.2)
model.add(Dense(1))
optimizer=optimizers.RMSprop(lr=0.0001)
model.compile(optimizer=optimizer,loss='mse',metrics=['mse'])
history=model.fit(x,y,batch_size=1,epochs=30,shuffle=False)
model.save('mkdir/pm1_0.h5')
'''
#PM2_5
'''
model=Sequential()
model.add(Dense(20,input_dim=2,activation='relu'))
Dropout(0.2)
model.add(Dense(20,activation='relu'))
Dropout(0.2)
model.add(Dense(20,activation='relu'))
Dropout(0.2)
model.add(Dense(1))
optimizer=optimizers.RMSprop(lr=0.0001)
model.compile(optimizer=optimizer,loss='mse',metrics=['mse'])
history=model.fit(x,y,batch_size=1,epochs=30,shuffle=False)
model.save('mkdir/pm2_5.h5')
'''

#PM10_0
'''
model=Sequential()
model.add(Dense(20,input_dim=2,activation='relu'))
Dropout(0.2)
model.add(Dense(20,activation='relu'))
Dropout(0.2)
model.add(Dense(20,activation='relu'))
Dropout(0.2)
model.add(Dense(1))
optimizer=optimizers.RMSprop(lr=0.0001)
model.compile(optimizer=optimizer,loss='mse',metrics=['mse'])
history=model.fit(x,y,batch_size=1,epochs=30,shuffle=False)
model.save('mkdir/pm10_0.h5')
plt.plot(history.history['loss'])
plt.title('model loss')
plt.ylabel('loss')
plt.xlabel('epoch')
plt.legend(['train'],loc='upper left')
plt.show()
'''


