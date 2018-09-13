// config.js
/**
 * 小程序后端接口配置文件
 */
var host = "";
var config = {
  //获取sessionKey
  get_session_key: host + '/api/get_session_key',
  //登录
  auth_login: host + '/api/auth_login',
  //首页
  trains: host + '/api/trains',
  //首页列表详情
  trains_show: host + '/api/trains_show',
  //报名培训声明，获取短信验证码
  send_code: host + '/api/send_code',
  //输入验证码，提交报名
  check_code: host + '/api/check_code',
  //通过合同号获取园所名称
  check_contract: host + '/api/check_contract',
  //添加学员
  save_nursery_students: host + '/api/save_nursery_students',
  //学员编辑
  nursery_students_edit: host + '/api/nursery_students_edit',
  //学员更新
  nursery_students_update: host + '/api/nursery_students_update',
  //上传图片
  upload_image: host + '/api/upload_image',
  //编辑页获取培训学员列表
  nursery_students: host + '/api/nursery_students',
  //编辑页获取培训学员列表-新增学员确认
  save_apply_students: host + '/api/save_apply_students',
  //报名详情删除培训学员
  apply_students_del: host + '/api/apply_students_del',
  //提交课程报名学员
  save_order: host + '/api/save_order',
  //订单详情
  order_detail: host + '/api/order_detail',
  //全部，待支付
  get_orders: host + '/api/get_orders',
  //取消报名
  cancel_order: host + '/api/cancel_order',
  //去支付
  go_pay: host + '/api/go_pay'
};
module.exports = config