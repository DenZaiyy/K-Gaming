function setScreenHWCookie() {
    Cookies.set('sw', screen.width, { expires: 7 })
    return true;
}
setScreenHWCookie();