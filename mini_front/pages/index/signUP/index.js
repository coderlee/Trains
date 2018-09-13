// pages/index/signUP/index.js
let config = require("../../../config.js");
let WxParse = require('./../../../wxParse/wxParse.js');
let common = require("./../../common.js");
Page({

  /**
   * 页面的初始数据
   */
  data: {
    canIUse: wx.canIUse('button.open-type.getUserInfo'),
    inittraninBmBefor:true,
    traninBmBefor: true,
    showBmPop: false,
    showBmPop_train: false,
    readStatement: true, //阅读培训声明判断
    readStatementC:false,
    time: '获取验证码', //倒计时
    trainS: '<<培训声明>> 请先阅读',
    showBtn: true,
    phone: '',
    train_id: '',
    interval: "",
    disPhone: false,
    selected: false,
    isAjax:false,
    user_openid:''
  },
  getIdDetail(id) {
    let _that = this;
    wx.request({
      url: config.trains_show + "/" + id,
      data: {
        r: Math.random()
      },
      method: 'GET',
      success: function(res) {
        let datas = res.data
        if (datas.code == "200") {
          /**
           * html解析示例
           */
          WxParse.wxParse('article', 'html', datas.data.desc, _that);
          WxParse.wxParse('article2', 'html', datas.data.desc_md, _that);
        }
      },

    })
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function(option) {
    
    if (option.train_id) {
      if (wx.getStorageSync('read_allow_list').includes(option.train_id)){
        this.setData({ inittraninBmBefor: false, traninBmBefor: false, readStatement: false, selected:true,readStatementC:true});
      }
      this.data.train_id = option.train_id;
      this.getIdDetail(option.train_id);
    } else {
      console.log("参数错误!");
    }
    this.setData({ user_openid: wx.getStorageSync('user_save_infos').openid})
  },

  showContent: function() {
    this.setData({
      showBmPop_train: true,
      readStatement: false
    });
  },
  closePhone() {
    clearInterval(this.data.interval);
    this.setData({
      showBmPop: false,
      showBtn: false,
      time: '获取验证码',
      disPhone: false
    });
  },
  closePop() {
    this.setData({
      showBmPop_train: false
    });
  },
  bindGetUserInfo: function(e) {
    let _that=this;
    if (e.detail.userInfo) {
      if (this.data.user_openid){
          if (!this.data.inittraninBmBefor) {
            wx.navigateTo({
              url: '/pages/index/signUP/signUpList/index?train_id=' + this.data.train_id
            })
          } else {
            this.showBmPop();
          }
      }else{
        wx.login({
          success: function (res) {
            let user_save_infos = {};
            if (res.code) {
              //发起网络请求
              wx.request({
                url: `${config.get_session_key}`,
                header: {
                  'content-type': 'application/x-www-form-urlencoded',
                  'Accept': 'application/json'
                },

                data: {
                  code: res.code
                },
                success: function (res) {
                  let user_save_infos = {};
                  let session_key = res.data.data.session_key;
                  wx.getUserInfo({
                    success: function (resL) {
                      wx.request({
                        url: `${config.auth_login}`,
                        method: 'POST',
                        data: {
                          encryptedData: resL.encryptedData,
                          iv: resL.iv,
                          sessionKey: session_key
                        },
                        success: function (resK) {
                          user_save_infos.openid = resK.data.data.openId;
                          user_save_infos.userid = resK.data.data.user_id;
                          _that.setData({ user_openid: resK.data.data.user_id })
                          wx.setStorage({
                            key: 'user_save_infos',
                            data: user_save_infos,
                            success: function (res) {
                              if (!_that.data.inittraninBmBefor) {
                                wx.navigateTo({
                                  url: '/pages/index/signUP/signUpList/index?train_id=' + _that.data.train_id
                                })
                              } else {
                                _that.showBmPop();
                              }
                              console.log("...");
                            }
                          })
                        }
                      })
                    }
                  })
                },
                fail: function () {
                  common.progressTips('信息获取失败！')
                }
              })
            } else {
              common.progressTips('登录失败！' + res.errMsg)
            }
          }
        });
      }
     
    }
  },
  showBmPop() {
    this.setData({
      showBmPop: true
    });
  },
  setHide() {
    this.setData({
      showBmPop: false
    });
  },
  agree() {
    if (!this.data.readStatement) {
      if (!this.data.selected) {
        this.setData({
          traninBmBefor: false,
          selected: true
        });
      } else {
        this.setData({
          traninBmBefor: true,
          selected: false
        });
      }

    }
  },
  testPhone(e) {
    let phoneReg = /^1\d{10}$/;
    this.setData({
      phone: e.detail.value
    });
    if (phoneReg.test(e.detail.value)) {
      this.setData({
        showBtn: false
      });
    } else {
      this.setData({
        showBtn: true
      });
    }
  },
  timeSE(timeM) {
    // 60s倒计时
    const that = this;
    let currentTime = timeM ? timeM : 60;
    that.data.interval = setInterval(function() {
      currentTime--;
      that.setData({
        time: currentTime + "s",
        disPhone: true
      })
      if (currentTime <= 0) {
        clearInterval(that.data.interval);
        that.setData({
          time: '获取验证码',
          currentTime: 60,
          showBtn: false,
          disPhone: false
        })
      }
    }, 1000);
  },
  getTextNum() {
    const that = this;
    that.setData({
      showBtn: true
    })
    wx.request({
      url: config.send_code,
      data: {
        r: Math.random(),
        apply_phone: that.data.phone
      },
      success: (res) => {
        let datas = res.data;
        if (datas.code == "200") {

          that.timeSE(60);
        }
        if (datas.code == "1008") {
          that.timeSE(datas.data.exipre);
        }
      },
      fail: (res) => {
        that.setData({
          showBtn: false,
          disPhone: false
        })
      }
    })
  },
  formSubmit: function(e) {
    const _that = this;
    let eV = e.detail.value;
    let codeReg = /\d{6}/;
    let phoneReg = /^1\d{10}$/;
    if (!phoneReg.test(eV.apply_phone)) {
      common.progressTips("请输入合法手机号！");
      return false;
    }
    if (!codeReg.test(eV.code)) {
      common.progressTips("请输入正确短信验证码！");
      return false;
    }
    this.setData({
      showBtn: true
    });
    if (this.data.isAjax){
      return false;
    }
    this.setData({
      isAjax: true
    });
    wx.request({
      url: config.check_code,
      method: 'POST',
      data: e.detail.value,
      success: (res) => {
        let datas = res.data;
        if (datas.code == "200") {
          _that.closePhone();
          let readAllow = wx.getStorageSync('read_allow_list') ? wx.getStorageSync('read_allow_list') + _that.data.train_id : _that.data.train_id;
          console.log(readAllow)
          wx.setStorage({
            key: 'read_allow_list',
            data: readAllow,
            success:function(){
              wx.navigateTo({
                url: '/pages/index/signUP/signUpList/index?train_id=' + _that.data.train_id,
              });
            }
          })
        } else {
          common.progressTips(datas.msg);
        }
      },
      fail: (res) => {
        common.progressTips('出错了！');
      },
      complete: (res) => {
        _that.setData({
          isAjax: false
        });
      }
    })
  },
  formReset: function() {
    console.log('form发生了reset事件')
  },
  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function() {

  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function() {},

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function() {

  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function() {

  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function() {

  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function() {

  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function() {

  }
})