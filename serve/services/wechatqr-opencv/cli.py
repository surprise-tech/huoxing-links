#!/usr/bin/env python3
import cv2
import sys
from cv2.wechat_qrcode import WeChatQRCode


def qr(new_width, new_height, mode):
    resized = cv2.resize(image, (new_width, new_height), interpolation=cv2.INTER_CUBIC)
    gray = cv2.cvtColor(resized, mode)  # cv2.COLOR_RGB2GRAY
    detect_obj = WeChatQRCode()
    results, _ = detect_obj.detectAndDecode(gray)

    if len(results) < 1:
        return ""

    return results[0]


try:
    image = cv2.imread(sys.argv[1])
    # 放大multiple倍，转灰度
    img_size = image.shape
    new_width = 2000
    new_height = int(img_size[0] * (new_width / img_size[1]))
    results = qr(new_width, new_height, cv2.COLOR_RGB2GRAY)
    if results == "":
        results = qr(new_width, new_height, cv2.COLOR_RGB2HSV)
        print(results)
        sys.exit(0)
    print(results)
    sys.exit(0)
except Exception as e:  # 捕获具体异常
    print(f"Error occurred: {str(e)}")
    sys.exit(1)
