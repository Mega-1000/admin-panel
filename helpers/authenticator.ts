import Cookies from "universal-cookie";

interface Data {
  access_token: string;
  refresh_token: string;
  expires_in: number;
}

function setCookie({ access_token, refresh_token, expires_in }: Data) {
  const cookies = new Cookies();
  let date = new Date();
  date = new Date(date.getTime() + expires_in * 1000);
  cookies.set("token", access_token, { expires: date });
  if (refresh_token) {
    cookies.set("refresh_token", refresh_token);
  }
}

function removeCookie() {
  // remove all cookies
  const cookies = document.cookie.split(";");

  for (let i = 0; i < cookies.length; i++) {
    const cookie = cookies[i];
    const eqPos = cookie.indexOf("=");
    const name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
    document.cookie = name + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
  }
}

function getToken() {
  const cookies = new Cookies();
  return cookies.get("token");
}

function getRefreshToken() {
  const cookies = new Cookies();
  return cookies.get("refresh_token");
}

export { setCookie, getToken, getRefreshToken, removeCookie };
