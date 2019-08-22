"""
* PMS7003 데이터 수신 프로그램
* 수정 : 2018. 11. 19
* 제작 : eleparts 부설연구소
* SW ver. 1.0.2

> 관련자료
파이썬 라이브러리
https://docs.python.org/3/library/struct.html

점프 투 파이썬
https://wikidocs.net/book/1

PMS7003 datasheet
http://eleparts.co.kr/data/_gextends/good-pdf/201803/good-pdf-4208690-1.pdf
"""
import serial
import struct
import time
from datetime import datetime


class PMS7003(object):

    # PMS7003 protocol data (HEADER 2byte + 30byte)
  PMS_7003_PROTOCOL_SIZE = 32

  # PMS7003 data list
  HEADER_HIGH            = 0  # 0x42
  HEADER_LOW             = 1  # 0x4d
  FRAME_LENGTH           = 2  # 2x13+2(data+check bytes) 
  DUST_PM1_0_CF1         = 3  # PM1.0 concentration unit μ g/m3（CF=1，standard particle）
  DUST_PM2_5_CF1         = 4  # PM2.5 concentration unit μ g/m3（CF=1，standard particle）
  DUST_PM10_0_CF1        = 5  # PM10 concentration unit μ g/m3（CF=1，standard particle）
  DUST_PM1_0_ATM         = 6  # PM1.0 concentration unit μ g/m3（under atmospheric environment）
  DUST_PM2_5_ATM         = 7  # PM2.5 concentration unit μ g/m3（under atmospheric environment）
  DUST_PM10_0_ATM        = 8  # PM10 concentration unit μ g/m3  (under atmospheric environment) 
  DUST_AIR_0_3           = 9  # indicates the number of particles with diameter beyond 0.3 um in 0.1 L of air. 
  DUST_AIR_0_5           = 10 # indicates the number of particles with diameter beyond 0.5 um in 0.1 L of air. 
  DUST_AIR_1_0           = 11 # indicates the number of particles with diameter beyond 1.0 um in 0.1 L of air. 
  DUST_AIR_2_5           = 12 # indicates the number of particles with diameter beyond 2.5 um in 0.1 L of air. 
  DUST_AIR_5_0           = 13 # indicates the number of particles with diameter beyond 5.0 um in 0.1 L of air. 
  DUST_AIR_10_0          = 14 # indicates the number of particles with diameter beyond 10 um in 0.1 L of air. 
  RESERVEDF              = 15 # Data13 Reserved high 8 bits
  RESERVEDB              = 16 # Data13 Reserved low 8 bits
  CHECKSUM               = 17 # Checksum code


  # header check 
  def header_chk(self, buffer):

      if (buffer[self.HEADER_HIGH] == 66 and buffer[self.HEADER_LOW] == 77):
          return True

      else:
          return False

  # chksum value calculation
  def chksum_cal(self, buffer):

      buffer = buffer[0:self.PMS_7003_PROTOCOL_SIZE]

    # data unpack (Byte -> Tuple (30 x unsigned char <B> + unsigned short <H>))
      chksum_data = struct.unpack('!30BH', buffer)

      chksum = 0

      for i in range(30):
          chksum = chksum + chksum_data[i]

      return chksum

# checksum check
  def chksum_chk(self, buffer):   

      chk_result = self.chksum_cal(buffer)

      chksum_buffer = buffer[30:self.PMS_7003_PROTOCOL_SIZE]
      chksum = struct.unpack('!H', chksum_buffer)

      if (chk_result == chksum[0]):
          return True

      else:
          return False

  # protocol size(small) check
  def protocol_size_chk(self, buffer):

      if(self.PMS_7003_PROTOCOL_SIZE <= len(buffer)):
          return True

      else:
          return False

  # protocol check
  def protocol_chk(self, buffer):

      if(self.protocol_size_chk(buffer)):

          if(self.header_chk(buffer)):

              if(self.chksum_chk(buffer)):
                   return True
              else:
                 print("Chksum err")
          else:
             print("Header err")
      else:
          print("Protol err")

      return False 

# unpack data 
  # <Tuple (13 x unsigned short <H> + 2 x unsigned char <B> + unsigned short <H>)>
  def unpack_data(self, buffer):

      buffer = buffer[0:self.PMS_7003_PROTOCOL_SIZE]

    # data unpack (Byte -> Tuple (13 x unsigned short <H> + 2 x unsigned char <B> + unsigned short <H>))
      data = struct.unpack('!2B13H2BH', buffer)
      

      return data



# UART / USB Serial : 'dmesg | grep ttyUSB'
USB0 = '/dev/ttyUSB0'
UART = '/dev/ttyAMA0'

# USE PORT
SERIAL_PORT = USB0

# Baud Rate
Speed = 9600


# example
if __name__=='__main__':
    #serial setting 
    ser = serial.Serial(SERIAL_PORT, Speed, timeout = 1)
    now=int(datetime.now().timestamp())
    dust = PMS7003()


    ser.flushInput()
    buffer = ser.read(1024)
    data = dust.unpack_data(buffer)
    if(dust.protocol_chk(buffer)):
        new_str=[]
        new_str.append(str(now))
        new_str.append(",")
        new_str.append(str(data[dust.DUST_PM1_0_ATM]))
        new_str.append(",")
        new_str.append(str(data[dust.DUST_PM2_5_ATM]))
        new_str.append(",")
        new_str.append(str(data[dust.DUST_PM10_0_ATM]))
        new_str=''.join(new_str)
        print(new_str)

    else:
        print("DATA read fail...")     


    ser.close()

