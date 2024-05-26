#!/bin/bash
# 清除緩存
chmod 775 ./cache
chmod 775 ./templates_c
chmod 775 ./account/templates_c/
chmod 775 ./admin/templates_c/
rm ./templates_c/* -rf
rm ./account/templates_c/* -rf
rm ./admin/templates_c/* -rf
rm ./cache/* -rf

# 測試
touch ./templates_c/test.log
touch ./cache/test.log

# 變更目錄權限
chown $USER:apache * -R
restorecon -Rv .

# 上傳檔案區域配置
sudo chmod 750 ./f -R
sudo chown apache ./f -R
sudo semanage fcontext -a -t httpd_sys_rw_content_t "./f(/.*)?"
sudo restorecon -RvF ./f
#sudo semanage fcontext -a -t httpd_sys_rw_content_t "./assets/uploads(/.*)?"
#sudo restorecon -RvF ./assets/uploads
