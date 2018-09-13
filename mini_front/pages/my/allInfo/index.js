// pages/my/allInfo/index.js
let config = require("../../../config.js");
let common = require("../../common.js");
Page({

  /**
   * 页面的初始数据
   */
  data: {
    times: "",
    status: "",
    train_info: '',
    setIntvalTime: '',
    isShowPay: false,
    noPass: false,
    train_id: '',
    order_id: '',
    ico_status: 'wait',
    isAjax: false
  },
  goIndex(){
    wx.switchTab({
      url: '/pages/index/index'
    })
  },
  //编辑
  goEdit(e) {
    let url = '/pages/index/signUP/signUpList/addInfo/index?edit_id=' + e.currentTarget.dataset.editid + "&source_url=/pages/my/allInfo/index&train_id=" + this.data.train_id + '&order_id=' + this.data.order_id;
    wx.navigateTo({
      url: url
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
  goPay() {
    if (this.data.isAjax) {
      return false;
    }
    this.data.isAjax = true;
    this.toWechatPay(this.data.order_id, this.data.train_id);
  },
  //订单状态
  orderStatus(o_s) {
    let _that = this;
    let s_n = '审核中';
    switch (o_s) {
      case 0:
        s_n = '已支付';
        _that.setData({
          noPass: false,
          ico_status: 'success'
        });
        break;
      case 1:
        s_n = '已退款';
        break;
      case 2:
        s_n = '已取消';
        _that.setData({
          noPass: false,
          ico_status: 'success'
        });
        break;
      case 3:
        s_n = '审核中';
        break;
      case 4:
        s_n = '审核未通过';
        _that.setData({
          noPass: true,
          ico_status: 'error'
        });
        break;
      case 5:
        s_n = '部分审核'
        break;
      case 6:
        s_n = '已审核';
        _that.setData({
          noPass: false,
          ico_status: 'success'
        });
        break;
      case 7:
        s_n = '已完成';
        _that.setData({
          noPass: false,
          ico_status: 'success'
        });
        break;
    }
    this.setData({
      status: s_n
    })
  },
  //倒计时转化
  timeF(times) {
    let _that = this;
    clearInterval(_that.data.setIntvalTime);
    _that.data.setIntvalTime = setInterval(function () {
      if (times > 0) {
        times--;
        let h = Number.parseInt(times / 3600);
        h = h >= 10 ? h : `0${h}`;
        let m = Number.parseInt(times / 60) - h * 60;
        console.log(h, m)
        m = m >= 10 ? m : `0${m}`;
        let s = times - h * 3600 - m * 60;
        s = s >= 10 ? s : `0${s}`;
        let time_of = `${h}:${m}:${s}`;
        _that.setData({
          times: time_of
        });
      } else {
        clearInterval(_that.data.setIntvalTime);
        _that.setData({
          times: '',
          isShowPay: false
        });
        _that.orderStatus(2);
      }
    }, 1000)
  },

  //订单详情
  getOrderDetail(order_id) {
    const _that = this;
    wx.request({
      url: `${config.order_detail}/${order_id}`,
      method: 'GET',
      success: function (res) {
        const datas = res.data;
        if (datas.code == "200") {
          const dd = datas.data;

          if (dd.is_paid) {
            //已支付
            _that.orderStatus(dd.status);
          } else {
            //未支付
            if (dd.status==2){
              _that.orderStatus(dd.status);
            }else{
              if (dd.surplus > 0) {
                //剩余支付倒计时  
                _that.setData({
                  isShowPay: true
                });
                _that.timeF(dd.surplus);
              }
            }
          }
          _that.setData({
            train_info: dd
          })
        } else {
          common.progressTips(datas.msg);
        }
      },
      fail: function () {
        common.progressTips("出错了！");
      }
    })
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    if (options.order_id) {
      this.setData({
        train_id: options.train_id,
        order_id: options.order_id
      });
      this.getOrderDetail(options.order_id);
    }
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {

  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {

  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function () {

  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function () {

  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {

  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {

  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {

  }
})