import re
import urllib.request
import urllib.error
import urllib.parse
import pymysql



#获取新歌榜所有歌曲名称和id
def get_all_newSong():
    url='http://music.163.com/discover/toplist?id=3779629'    #网易云云音乐新歌榜url
    header={    #请求头部
        'User-Agent':'Mozilla/5.0 (X11; Fedora; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36'
    }
    request=urllib.request.Request(url=url, headers=header)
    html=urllib.request.urlopen(request).read().decode('utf8')   #打开url
    html=str(html)     #转换成str
    pat1=r'<ul class="f-hide"><li><a href="/song\?id=\d*?">.*</a></li></ul>'  #进行第一次筛选的正则表达式
    result=re.compile(pat1).findall(html)     #用正则表达式进行筛选
    result=result[0]     #获取tuple的第一个元素

    pat2=r'<li><a href="/song\?id=\d*?">(.*?)</a></li>' #进行歌名筛选的正则表达式
    pat3=r'<li><a href="/song\?id=(\d*?)">.*?</a></li>'  #进行歌ID筛选的正则表达式
    new_song_name=re.compile(pat2).findall(result)    #获取所有热门歌曲名称
    new_song_id=re.compile(pat3).findall(result)    #获取所有热门歌曲对应的Id

    return new_song_name,new_song_id

# 获取热歌榜所有歌曲名称和id
new_song_name,new_song_id=get_all_newSong()
# 打开数据库连接
# db = pymysql.connect(host="w.rdc.sae.sina.com.cn",port=3306, user="w2w044wxx4", passwd="w5z14my332mklz33zjiki4yj34kx0mx5l20i11k4", db="app_yujiage2")
db = pymysql.connect(host="localhost",port=3306, user="root", passwd="123456", db="netease", charset="utf8")
# 使用 cursor() 方法创建一个游标对象 cursor+
cursor = db.cursor()
# 打印所有歌曲名字：歌曲Id
num=0
errorNum=0
for item in new_song_name:
    # SQL 插入语句
    sql = "INSERT INTO 09NEW_MUSIC(MUSIC_RANK, \
             MUSIC_NAME, MUSIC_URL) \
             VALUES ('%d', '%s', '%s')" % \
          (num+1, new_song_name[num], 'http://music.163.com/song?id='+new_song_id[num])
    try:
        cursor.execute(sql)# 执行sql语句
        db.commit()# 提交到数据库执行
    except:
        print('%s:添加失败！'%(new_song_name[num]))
        errorNum+=1
        db.rollback()# 如果发生错误则回滚
    # print('%d、%s:http://music.163.com/song?id=%s'%(num, hot_song_name[num] ,hot_song_id[num]))
    num+=1
print('成功插入 %d 条数据'%(num-errorNum))
# 关闭数据库连接
db.close()