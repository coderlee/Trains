// pages/my/index.js
let config = require("../../config.js");
let common = require("../common.js");
Page({

  /**
   * 页面的初始数据
   */
  data: {
    tabCur: '0',
    userid: '',
    dataList: [],
    canIUses: false,
    order_status: '',
    list: [{
      title: "初级培训"
    }]
  },
  chooseTab(e) {
    let tabCurIndex = e.currentTarget.dataset.tab;
    this.getMyorder(tabCurIndex);
    this.setData({
      tabCur: tabCurIndex
    });
  },
  bindGetUserInfo: function(e) {
    let _that=this;
    if (e.detail.userInfo) {
      this.setData({
        canIUses: false
      });
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
                        _that.setData({ userid: resK.data.data.user_id})
                        wx.setStorage({
                          key: 'user_save_infos',
                          data: user_save_infos,
                          success: function (res) {
                            _that.getMyorder();
                            console.log("...");
                          }
                        })
                      }
                    })
                  }
                })
              },
              fail: function () {
                console.log('信息获取失败！')
              }
            })
          } else {
            console.log('登录失败！' + res.errMsg)
          }
        }
      });
      
    } else {
      this.setData({
        canIUses: true
      });
    }
  },
  bmCancel(e) {
    let _that = this;
    let order_id = e.currentTarget.dataset.id;
    wx.showModal({
      content: '确认取消本次培训报名？',
      success: function(res) {
        if (res.confirm) {
          wx.request({
            url: `${config.cancel_order}/${order_id}`,
            success: function(res) {
              let dataD = res.data;
              if (dataD.code == "200") {
                if (_that.data.tabCur == 1) {
                  _that.getMyorder(1);
                } else {
                  _that.getMyorder();
                }

              } else {
                common.progressTips(dataD.msg);
              }
            },
            fail: function() {
              common.progressTips("出错了！");
            }
          })
        } else if (res.cancel) {
          console.log('用户点击取消');
        }
      }
    })
  },
  toWechatPay: function (order_id, train_id) {
    var that = this
    wx.request({
      url: config.go_pay,
      method: "post",
      data: {
        order_id: order_id
      },
      header: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      success: function (res) {
        let datas_d = res.data;
        let order = datas_d;
        wx.requestPayment({
          timeStamp: order.timeStamp,
          nonceStr: order.nonceStr,
          package: order.package,
          signType: 'MD5',
          paySign: order.paySign,
          success: function (res) {
            common.progressTips('支付成功！');
          },
          fail: function (res) {
            common.progressTips('支付失败！');
          },
          complete: function (res) {
            setTimeout(function () {
              wx.navigateTo({
                url: `/pages/my/allInfo/index?order_id=${order_id}&train_id=${train_id}&contract_no=${that.data.contract_no}`,
              })
            }, 2000);
          }
        })
      }
    })
  },
  goPay(e) {
    let order_id = e.currentTarget.dataset.id;
    let train_id = e.currentTarget.dataset.trainid;
    if(this.data.isAjax){
      return false;
    }
    this.data.isAjax=true;
    this.toWechatPay(order_id, train_id);
  },
  //获取全部，待支付列表
  getMyorder(is_paid) {
    wx.showLoading({
      title: '加载中',
      icon: 'loading',
    })
    let _that = this;
    let parmas = {
      "apply_user": _that.data.userid
    };
    if (is_paid == 1) {
      parmas.is_paid = 0;
    }

    wx.request({
      url: `${config.get_orders}`,
      method: 'GET',
      data: parmas,
      success: function(res) {
        _that.setData({
          dataList: res.data.data
        });
        setTimeout(function(){
          wx.hideLoading();
        },500)
      },
      fail: function() {
        common.progressTips("出错了！");
      }
    })
  },
  goTrainDetail(e) {
    let _that = this;
    let order_id = e.currentTarget.dataset.id;
    let train_id = e.currentTarget.dataset.trainid;
    wx.navigateTo({
      url: `/pages/my/allInfo/index?order_id=${order_id}&train_id=${train_id}`,
    })
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function(options) {
    let _that = this;
    wx.getSetting({
      success: function(res) {
        if (res.authSetting['scope.userInfo']) {
          // 已经授权，可以直接调用 getUserInfo 获取头像昵称
          _that.data.userid = wx.getStorageSync('user_save_infos').userid;
          _that.getMyorder();
        } else {
          _that.setData({
            canIUses: true
          });
        }
      }
    })
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function() {

  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function() {

  },

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