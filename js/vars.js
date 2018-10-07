vars = {
  Status: {
      nochange: -1,
      success: 0,
      normal: 0,
      error: 1,
      needargs: 2
  }
};
vars.Regexp = {
  email: /^[A-Za-z\d]+([-_.][A-Za-z\d]+)*@([A-Za-z\d]+[-.])+[A-Za-z\d]{2,6}$/,
  usrid: /^[a-zA-Z][a-zA-Z0-9_]+$/,
  getid: /([a-zA-Z][a-zA-Z0-9_]+)/,
  getargs: /^.*?\/.*?\/(.*)$/,
  getcargs: /^.*?\/.*?\/.*?\/(.*)$/,
  numonly: /^\d+$/,
  getnum: /(\d+)/
};
vars.UserStatus = {
  success: vars.Status.success,
  nochange: vars.Status.nochange,
  failed: vars.Status.error,
  needlogin: 0x11,
  usererror: 0x12,
  iderror: 0x13,
  needid: 0x14,
  notfound: 0x16,
  needpermission: 0x17
};
vars.RegStatus = {
  success: vars.Status.success,
  failed: vars.Status.error,
  argerr: 0x21,
  idused: 0x22,
  emailerr: 0x23,
  iderr: 0x24
};
vars.UserGroup = {
  normal: 0,
  normalstr: '正常',
  banned: 1,
  bannedstr: '封禁',
  guest: 2,
  gueststr: '游客',
  admin: 3,
  adminstr: '管理员'
};
vars.Permission = {
  read: 0b1,
  readstr: '查看',
  rep: 0b10,
  repstr: '回复',
  newpost: 0b100,
  newpoststr: '发帖',
  admin: 0b1000,
  adminstr: '管理员'
};
vars.PostStatus = {
  success: vars.Status.success,
  nochange: vars.Status.nochange,
  error: vars.Status.error,
  noown: 0x31,
  needpostid: 0x32,
  needmaster: 0x33,
  needtitle: 0x34,
  needtext: 0x35,
  notfound: 0x36,
  needpermission: 0x37,
  iderror: 0x38
};
function addCookie(objName, objValue, objExp){
  var ms = objExp||3600*34*30*1000;
  var str = objName + "=" + escape(objValue);
  var date = new Date();
  date.setTime(date.getTime() + ms);
  str += "; expires=" + date.toGMTString() + "; path=/";
  document.cookie = str;
}
function addInfCookie(objName, objValue){
  addCookie(objName, objValue, "Fri, 31 Dec 9999 23:59:59 GMT");
}
function getCookie(name){
  var arr,reg = new RegExp("(^| )"+name+"=([^;]*)(;|$)");
  if (arr=document.cookie.match(reg))
    return unescape(arr[2]);
  else return null;
}
function delCookie(name){
  var exp= new Date();
  exp.setTime(exp.getTime() - 1);
  var n = getCookie(name);
  if (n)
    document.cookie = name+"="+n+"; expires="+exp.toGMTString();
}
function dateFtt(fmt,date){
  var o = {
    "M+" : date.getMonth()+1,
    "d+" : date.getDate(),
    "h+" : date.getHours(),
    "m+" : date.getMinutes(),
    "s+" : date.getSeconds(),
    "q+" : Math.floor((date.getMonth()+3)/3),
    "S"  : date.getMilliseconds()
  };
  if(/(y+)/.test(fmt))
    fmt=fmt.replace(RegExp.$1, (date.getFullYear()+"").substr(4 - RegExp.$1.length));
  for(var k in o)
    if(new RegExp("("+ k +")").test(fmt))
  fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));
  return fmt;
}
var cfg = {
  sliderdelay: 5000
};
function getid(usrid){
  t = usrid.match(vars.Regexp.getid);
  if (t==null)
    return t;
  else return t[0];
}
function getnum(str){
  t = str.match(vars.Regexp.getnum);
  if (t==null)
    return t;
  else return parseInt(t[0]);
}
function getuid(usrid){
  u = Object.keys(mv.users);
  for (i in u){
    if (mv.users[u].usrid==usrid)
      return i;
  }
}
function getusrdata(uid, key){
  u = Object.keys(mv.users);
  ui = u.indexOf('u'+uid);
  if (ui!==-1)
    return mv.users[u[ui]][key];
  return "";
}
function trim(s){
  return s.replace(/^\s+|\s+$/gm, '');
}
function trim2(s){
  if (/^\s+$/.test(s)) return '';
  else return s;
}
function cutch(str){
  if (/^中国.*$/.test(str))
    return str.substr(2);
  return str;
}
