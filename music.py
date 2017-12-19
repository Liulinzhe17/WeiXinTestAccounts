import re
import urllib.request
import urllib.error
import urllib.parse
import json
import pymysql
import time



#获取热歌榜所有歌曲名称和id
def get_all_hotSong():
    url='http://music.163.com/discover/toplist?id=3778678'    #网易云云音乐热歌榜url
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
    hot_song_name=re.compile(pat2).findall(result)    #获取所有热门歌曲名称
    hot_song_id=re.compile(pat3).findall(result)    #获取所有热门歌曲对应的Id

    return hot_song_name,hot_song_id

#获取热门评论
def get_hotComments(hot_song_name,hot_song_id):
    url='http://music.163.com/weapi/v1/resource/comments/R_SO_4_' + hot_song_id + '?csrf_token='   #歌评url
    header={    #请求头部
   'User-Agent':'Mozilla/5.0 (X11; Fedora; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36'
}
    #post请求表单数据
    data={'params':'zC7fzWBKxxsm6TZ3PiRjd056g9iGHtbtc8vjTpBXshKIboaPnUyAXKze+KNi9QiEz/IieyRnZfNztp7yvTFyBXOlVQP/JdYNZw2+GRQDg7grOR2ZjroqoOU2z0TNhy+qDHKSV8ZXOnxUF93w3DA51ADDQHB0IngL+v6N8KthdVZeZBe0d3EsUFS8ZJltNRUJ','encSecKey':'4801507e42c326dfc6b50539395a4fe417594f7cf122cf3d061d1447372ba3aa804541a8ae3b3811c081eb0f2b71827850af59af411a10a1795f7a16a5189d163bc9f67b3d1907f5e6fac652f7ef66e5a1f12d6949be851fcf4f39a0c2379580a040dc53b306d5c807bf313cc0e8f39bf7d35de691c497cda1d436b808549acc'}
    postdata=urllib.parse.urlencode(data).encode('utf8')  #进行编码
    request=urllib.request.Request(url,headers=header,data=postdata)
    reponse=urllib.request.urlopen(request).read().decode('utf8')
    json_dict=json.loads(reponse)   #获取json
    hot_commit=json_dict['hotComments']  #获取json中的热门评论

    likedCount=hot_commit[0]['likedCount']
    content=hot_commit[0]['content']
    print('%s;赞：%d;%s'%(hot_song_name,likedCount,content))
    return likedCount,content



# 获取热歌榜所有歌曲名称和id
hot_song_name,hot_song_id=get_all_hotSong()

db = pymysql.connect(host="localhost", port=3306, user="root", passwd="123456", db="netease", charset="utf8")
cursor = db.cursor()
# 保存所有热歌榜中的热评
num=0
errorNum=0
while num < len(hot_song_name):
    print('正在抓取第%d首歌曲热评...'%(num+1))
    likedCount,content=get_hotComments(hot_song_name[num],hot_song_id[num])
    print('第%d首歌曲热评抓取成功' % (num + 1))
    #保存到数据库
    sql = "INSERT INTO HOT_COMMENT(SONG_NAME, \
             LIKED_COUNT, CONTENT) \
             VALUES ('%s', '%d', '%s')" % \
          (hot_song_name[num], likedCount, content)
    try:
        cursor.execute(sql)  # 执行sql语句
        db.commit()  # 提交到数据库执行
    except:
        print('第%d首歌曲:添加失败！' %(num + 1))
        errorNum+=1
        db.rollback()  # 如果发生错误则回滚
    num+=1
    # time.sleep(2)
# 关闭数据库连接
db.close()
print('成功插入 %d 条数据' % (num - errorNum))

# # 获取热歌榜所有歌曲名称和id
# hot_song_name,hot_song_id=get_all_hotSong()
# # 打开数据库连接
# # db = pymysql.connect(host="w.rdc.sae.sina.com.cn",port=3306, user="w2w044wxx4", passwd="w5z14my332mklz33zjiki4yj34kx0mx5l20i11k4", db="app_yujiage2")
# db = pymysql.connect(host="localhost",port=3306, user="root", passwd="123456", db="netease", charset="utf8")
# # 使用 cursor() 方法创建一个游标对象 cursor+
# cursor = db.cursor()
# # 打印所有歌曲名字：歌曲Id
# num=0
# errorNum=0
# for item in hot_song_name:
#     # SQL 插入语句
#     sql = "INSERT INTO HOT_MUSIC(MUSIC_RANK, \
#              MUSIC_NAME, MUSIC_URL) \
#              VALUES ('%d', '%s', '%s')" % \
#           (num+1, hot_song_name[num], 'http://music.163.com/song?id='+hot_song_id[num])
#     try:
#         cursor.execute(sql)# 执行sql语句
#         db.commit()# 提交到数据库执行
#     except:
#         print('%s:添加失败！'%(hot_song_name[num]))
#         errorNum+=1
#         db.rollback()# 如果发生错误则回滚
#     # print('%d、%s:http://music.163.com/song?id=%s'%(num, hot_song_name[num] ,hot_song_id[num]))
#     num+=1
# print('成功插入 %d 条数据'%(num-errorNum))
# # 关闭数据库连接
# db.close()
