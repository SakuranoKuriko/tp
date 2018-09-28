vars = {
    Status: {
        success: 0,
        normal: 0,
        error: 1
    }
};
vars.Regexp = {
    email: /^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.[a-zA-Z0-9]: {2,6}$/,
    usrid: /^[a-zA-Z][a-zA-Z0-9_]+$/,
    getid: /([a-zA-Z][a-zA-Z0-9_]+)/,
    getargs: /^.*?\/.*?\/(.*)$/,
    getcargs: /^.*?\/.*?\/.*?\/(.*)$/,
    numonly: /^\d+$/,
    getnum: /(\d+)/
};
vars.UserStatus = {
    success: vars.Status.success,
    failed: vars.Status.error,
    needlogin: 0x11,
    usererror: 0x12,
    iderror: 0x13,
    needid: 0x14,
    notfound: 0x16
};
vars.RegStatus = {
    success: vars.Status.success,
    failed: vars.Status.error,
    argerr: 0x21,
    idused: 0x22,
    emailerr: 0x23
};
vars.UserGroup = {
    normal: 0,
    banned: 1
};
vars.Permission = {
    read: 0b1,
    write: 0b10,
    post: 0b100,
    admin: 0b1000
};
vars.PostStatus = {
    success: vars.Status.success,
    nochange: -1,
    error: vars.Status.error,
    noown: 0x31,
    needpostid: 0x32,
    needmaster: 0x33,
    needtitle: 0x34,
    needtext: 0x35,
    notfound: 0x36,
    iderror: 0x37
};