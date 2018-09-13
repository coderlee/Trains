function is_weixn() {
    var ua = navigator.userAgent.toLowerCase();
    if (ua.match(/MicroMessenger/i) == "micromessenger") {
      return true;
    } else {
      return false;
    }
  }
// if(!is_weixn()){
//   alert("请用微信扫描二维码");
//   window.open("./../../errorPage.html")
// }