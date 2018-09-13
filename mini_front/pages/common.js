function progressTips(tips) {
  wx.showToast({
    title: tips,
    icon: 'none',
    duration: 2000
  })
}
/**
 * 加载
 */
function showLoading() {
  wx.showToast({
    icon: 'loading',
    title: '加载中',
    mask: true
  })
}

/**
 * 隐藏加载
 */
function hideLoading() {
  wx.hideLoading()
}

module.exports = {
  progressTips: progressTips,
  showLoading: showLoading,
  hideLoading: hideLoading
}