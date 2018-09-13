// pages/index/signUP/signUpList/editInfo/index.js
let config = require("../../../../../config.js");
let common = require("../../../../common.js");
Page({

  /**
   * 页面的初始数据
   */
  data: {
    studentList: [],
    selectEditArr: [],
    source_url: '',
    contract_no: '',
    train_id: '',
    type_unit: '',
    unit_num: '',
    isAjax:false,
    empty:0
  },

  removeArrIndex(arr, val) {
    var indexA = arr.indexOf(val);
    if (indexA > -1) {
      arr.splice(indexA, 1);
    }
  },
  //获取列表数据
  getList() {
    let _that = this;
    wx.request({
      url: config.nursery_students,
      method: "GET",
      data: {
        r: Math.random(),
        contract_no: _that.data.contract_no,
        train_id: _that.data.train_id,
        apply_user: wx.getStorageSync('user_save_infos').userid
      },
      success: function(res) {
        let datas = res.data;
        if (datas.code == "200") {
          for (let y = 0; y < datas.data.length; y++) {
            if (datas.data[y].is_apply) {
              _that.data.selectEditArr.push(datas.data[y].id);
            }
          }
          _that.setData({
            studentList: datas.data
          });
        } else {
          common.progressTips(datas.msg);
        }
      },
      fail: function() {
        common.progressTips('出错了！');
      }
    })
  },
  checkEdit(e) {
    let arrCheckIndex = this.data.selectEditArr;
    let index = e.currentTarget.dataset.index;
    let id = e.currentTarget.dataset.id;
    let datas = this.data.studentList;
    if (this.data.type_unit == 2) {
      //园所类型
      if (!arrCheckIndex.includes(id)&&arrCheckIndex.length >= this.data.unit_num) {
        common.progressTips("该课程限制人数为" + this.data.unit_num + "人");
        return false;
      }
    }

    if (arrCheckIndex.indexOf(id) > -1) {
      this.removeArrIndex(arrCheckIndex, id)
      datas[index].is_apply = 0;
    } else {
      arrCheckIndex.push(id);
      datas[index].is_apply = 1;
    }
    this.setData({
      selectEditArr: arrCheckIndex,
      studentList: datas
    })
  },
  goSignUpListIndex(e) {
    let _that = this;
    let index = parseInt(e.currentTarget.dataset.index);
    let id = parseInt(e.currentTarget.dataset.id);
    let goUrl = "/pages/index/signUP/signUpList/index?source_url=/pages/index/signUP/signUpList/editInfo/index&contract_no=" + _that.data.contract_no+"&edit_id=" + id + "&train_id=" + _that.data.train_id;
    if (index != 10000000) {
      goUrl = "/pages/index/signUP/signUpList/addInfo/index?source_url=/pages/index/signUP/signUpList/editInfo/index&contract_no=" + _that.data.contract_no+"&edit_id=" + id + "&train_id=" + _that.data.train_id;
      wx.navigateTo({
        url: goUrl
      });
    } else {
      if(_that.data.isAjax){
        return false;
      }
      _that.setData({ isAjax:true});
      wx.request({
        url: config.save_apply_students,
        method: "POST",
        data: {
          contract_no: _that.data.contract_no,
          train_id: _that.data.train_id,
          student_id: _that.data.selectEditArr.toString(),
          apply_user: wx.getStorageSync('user_save_infos').userid
        },
        success: function(res) {
          let datas = res.data;
          if (datas.code == "200") {
            wx.navigateTo({
              url: goUrl
            });
          } else {
            common.progressTips(datas.msg);
          }
        },
        fail: function() {
          common.progressTips('出错了！');
        },
        complete:function(){
          _that.setData({ isAjax: false });
        }
      })

    }
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function(options) {
    if (options.empty){
      this.setData({
        empty: options.empty
      });
    }
    this.setData({
      source_url: options.source_url,
      train_id: options.train_id,
      type_unit: options.type_unit,
      unit_num: options.unit_num,
      contract_no: options.contract_no
    });
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