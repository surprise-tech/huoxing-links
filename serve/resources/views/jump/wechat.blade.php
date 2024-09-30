<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8" />
    <meta name="renderer" content="webkit" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style>
        html body {
            margin: 0;
        }

        .main {
            min-height: 100vh;
            width: 100%;
        }

        .flex {
            display: flex;
            align-items: center;
        }

        .flex-direction {
            flex-direction: column;
        }

        .align-center {
            align-items: center;
        }

        .justify-center {
            justify-content: center;
        }

        .justify-between {
            justify-content: space-between;
        }

        .safe-icon {
            height: 19px;
            width: 19px;
        }

        .ssl {
            color: rgb(116, 116, 116);
            font-size: 12px;
            margin-left: 5px;
        }

        .wx-logo {
            width: 75px;
            height: 75px;
        }

        .jump-tip {
            margin-top: 15px;
            color: rgb(139, 139, 139);
            font-size: 12px;
        }

        .open {
            margin-top: 8px;
            font-size: 14px;
            color: rgb(84, 84, 84);
        }

        .btn {
            margin-top: 50px;
            width: 250px;
            height: 40px;
            line-height: 1;
            padding: 0;
            background-size: 100% 100%;
            font-size: 14px;
            background-color: rgb(7, 193, 96);
            color: rgb(255, 255, 255);
            border-radius: 25px;
            background-image: none;
            border: 0 solid rgb(64, 158, 255);
        }

        .btn-disable {
            background-color: rgb(123, 121, 121);
        }
    </style>
    <title></title>
</head>

<body>
    <div class="main flex flex-direction align-center justify-between">
        <div class="flex align-stretch justify-center" style="padding-top: 15px">
            <img class="safe-icon" alt="safe icon" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIAAAACACAMAAAD04JH5AAAABGdBTUEAALGPC/xhBQAAACBjSFJN
AAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAACHFBMVEUAAAAwu2czv2wyvGkx
vGkxvGoxvGkyvGkyvGgwv3AxvWoxvGkyvWgyu2kzvW0xvGkxvGkwu2gyvWozuWsxu2k5xnExu2gy
vGkwvmsxvGkuv20yvWkyvWkxu2oxvGkwumoAgIAyu2oxvGkxumsxvGkwu2gyv2kxvGcvvWgnsWIx
vGkxvGkwvGkxvGozvWszzGYxvGkxu2gyvGkuuXQxvGkxvWgwvGk1v2oxvGkxu2kxvWsxvGkwvGky
vWoxvWkwvGgxvGkxvWkwvGowvGkyvWgxvGgzu2YxvGkvu2gxu2g1vGkxvGkxu2gwvGozvWYxvGkw
u2gk220xvGszvmsxvGkyumgxvGo2vGsxvWkwvGoxvGkyu2kxvGgq1YAwu2kxvWgyvWowvGkxvGkx
vGmA1qO058i+6tCY3rS66c3///+Y3bTc9ObW8uG86c4yvGq96c8zvWs1vWzA69E2vm3B69I3vm5B
wXXQ8N31/Pjg9emo47/C69M4vm7L7tnE7NQ6v2/G7dbF7NU8v3Hy+/bC69I8wHG+6s/I7dc+wHK5
6MzJ7dhAwXS158nK7tnH7daw5cXM7tpCwnXD7NOt5MTN79tEwnex5sbP79xGw3i46Mu36MvR8N1H
w3nT8d9IxHrU8eBKxHut5MPV8eBLxHyv5cXX8uJMxX2x5sfY8uNOxX7Z8+NPxn+258rb8+VRxoBS
x4Gr5MJTx4JlzY9Fw3hMbaEQAAAAY3RSTlMATyiK7GPFPZ8QeNpRtCON72XIN6IJeN1KtxyL8l7M
MAJx4EOyQDgqGw390cOYMgX5k2YL7aZfGPStH/qzbOqJ95Y1o0KwD9BWnSLkaa4j3IAHOTdEUc0T
m2rzOMEGj12uecn6bFTUAAAAAWJLR0RpvGvEtAAAAAd0SU1FB+gHGgIuHKfF2akAAAPMSURBVHja
7drnUxNBGAbwl14EDE3Agop0EESKSrUjHaUXKS4gEbFgQVEUu9grNuy9t3/QhCQH5G7vsuXuxpl9
PmVmmff3sLlkNskBiIiIiIiIiPzn8fIyU/f28UXI18fbJN7PHznj72e8HhCI5iUwwEg9KHgBkmVB
cJBBfEgowiQ0RH89bKEFqcSyMExXPjwCaSYiXC89MkpbdyQqkr8evSjGU96emEXRXPnYOBLdkbhY
XvriJUvJeXuWLlnMgV8WT6c7Er+MTV8eyKI7EricVl+xMoGdtydh5QoKflUiH92RxFWkfhJP3p4k
UUAUEAVEAQML9PUP7DWzwKDVHvMKOHzrPrMKOH3rkEkFXL51P2OBZEbf2ue2kkxYIIXRH3RfSiEs
kMrmDx9wX0slLJDG5B88JFtMIywA6Qz+wGHZYjqpDxZ6f+SIfNVCXID0RDzrHz2msJxAXCBDPuT4
6ImTY5r+qdNK6xnEBTLlQ2bmj2v4Z84q/kEmcYHVshkjDmFC1T93XnmHVhMX8JPNuOA0Lqr4E5cw
TxH592hZshknXcplrH/lKsZHWcQFste4zxiTnEmMf+06zl+TTVwA/GVTxiXphqJ/E+sjf3IfcuRj
bknWbQX/zl2sj3IoCqxVmHNP0u7L/AcP8T5aS1FA8UQwInlTbv4jNZ/4NGBPbp7SpGFJ7J/njz5W
8/NyKQpAvuKsJ5L5dI7/bFrNR/k0PhQoD7sxe+qRHj1/oeqjAqoC69YrT5uyuuflK3V//TqqArAB
M++1m//mrbqPNtD5ii/Embyb57//oOFTvQjtKSzCTfw4x//0WcsvKqQsAMXYmf2S/+Wrlo+KaX0o
wQ/95vS//9D0UQl1gdRS/NTJGX/op7ZfSvqRYE7KVOb+svlPfmv7qIzeB++NKoOn//z1gEcbmX7X
3OQJoZ5NLD5sZi+wmakAbGH1t7D5sJW1wFbGArCNzd/G6rNuAfMGqL8XaIblPcCV7eX0fvl2DgVg
B32BHTx8gApav4KPD5VVdH5VJacCUE1XoJqXD1BD49fw86G2jtyvq+VYAELIC3C+n6Se1K/n6xNf
BjwvAGeILoM6/j7sJCmwU4cCsMtzf5cePkCDp36DPj5ApGe+DncRuRLgia/rfXWN2n6jnj5ArJbP
7fYlXJrU/Sa9fduz0Iznm3Xef0daWnF+a4sRvi1t7Up8e5tBvC0dnXK/s8M4H2B3l7vftdtI35bu
eSfVqm6DeVu8e2b9HnNuru51+b2m8Lak7LHze0h/GOeZeh0Of4SbYOa/LyIiIiIiIiLCJf8A7XxW
+GRR4HcAAAAldEVYdGRhdGU6Y3JlYXRlADIwMjQtMDctMjZUMDI6NDY6MjgrMDA6MDAx/3gOAAAA
JXRFWHRkYXRlOm1vZGlmeQAyMDI0LTA3LTI2VDAyOjQ2OjI4KzAwOjAwQKLAsgAAACh0RVh0ZGF0
ZTp0aW1lc3RhbXAAMjAyNC0wNy0yNlQwMjo0NjoyOCswMDowMBe34W0AAAAASUVORK5CYII=">
            <div class="ssl">本链接已经过SSL安全加密，请放心点击</div>
        </div>
        <div class="mainCard flex flex-direction align-center">
            <img id="debug" class="wx-logo" alt="wechat logo" src="data:image/png;base64,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
SUVORK5CYII=" />
            <div class="jump-tip">正在跳转到微信...</div>
            <div class="open">如未自动打开微信请点击下方按钮</div>
            <button id="jump-btn" class="default btn" onclick="jump()">
                点击立即前往微信
            </button>
        </div>
        <div class="jump-tip" style="padding-bottom: 30px;">
            火星智慧引流提供技术服务
        </div>
    </div>
    <script type="text/javascript">
        var targetResponse = undefined;
        var code = window.location.pathname.split('/').pop();
        if (code) {
            fetch("/api/link-target/" + code)
                .then(function(response) {
                    return response.json();
                })
                .then(function(data) {
                    if (data.message) {
                        document.querySelector('#jump-btn').innerText = data.message;
                        document.querySelector('#jump-btn').classList.add('btn-disable');
                    } else {
                        window.parent.postMessage(data, '*');
                        targetResponse = data;
                        jump();
                    }
                })
        }

        function jump() {
            if (targetResponse) {
                window.location.href = targetResponse.target;
            }
        }

        function loadVConsole() {
            const script = document.createElement('script');
            script.src = "https://unpkg.com/vconsole@latest/dist/vconsole.min.js";
            script.onload = function() {
                new window.VConsole();
            };
            document.body.appendChild(script);
        }

        let clickCount = 0;
        document.getElementById('debug').addEventListener('click', function() {
            clickCount++;
            if (clickCount === 5) {
                loadVConsole();
            }
        });
    </script>
</body>

</html>
