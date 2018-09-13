// pages/index/index.js
let config = require("../../config.js");
Page({

  /**
   * 页面的初始数据
   */
  data: {
    message: 'Hello MINA!',
    list: [],
    pay_type_list: []
  },
  compare(property) {
    return function(obj1, obj2) {
      var value1 = obj1[property];
      var value2 = obj2[property];
      return value1 - value2; // 升序
    }
  },
  /*获取首页列表数据*/
  getList() {
    let _that = this;
    wx.request({
      url: config.trains,
      data: {
        r: Math.random()
      },
      method: 'GET',
      success: function(res) {
        let datas = res.data
        if (datas.code == "200") {
          wx.showLoading({
            title: '加载中',
            success:function(){
              let datas_list_deil = datas.data;
              let new_datas = [];
              for (let y = 0; y < datas_list_deil.length; y++) {
                let thatOne = datas_list_deil[y];
                let thsiOnePrics = [];
                if (thatOne.is_free) {
                  const course_detail = thatOne.get_charge;
                  for (let i = 1; i < 4; i++) {
                    let arN = "attr" + i + "_name";
                    let prC = "attr" + i + "_price";
                    if (course_detail[arN]) {
                      thsiOnePrics.push({
                        "attr_name": course_detail[arN],
                        "attr_price": course_detail[prC]
                      });
                    }
                  }
                  //重新排序
                  thsiOnePrics = thsiOnePrics.sort(_that.compare("attr_price"));
                  thatOne.getPrices = thsiOnePrics;
                }
                new_datas.push(thatOne);
              }
              _that.setData({
                list: new_datas
              });
              setTimeout(function(){
                wx.hideLoading();
              },1000)
            }
          })
        }
      },

    })
  },
  goUpIndex(e) {
    let id = e.currentTarget.dataset.id;
    let index = e.currentTarget.dataset.index;
    let course_one_detail = this.data.list[index] || '';
    wx.setStorage({
      key: 'course_one_detail',
      data: course_one_detail,
      success: function(res) {
        let url = '/pages/index/signUP/index?train_id=' + id;
        wx.navigateTo({
          url: url
        })
      }
    })
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function(options) {
    //授权

    this.getList();
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