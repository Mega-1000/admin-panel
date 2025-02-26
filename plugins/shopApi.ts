import axios from "axios";
import {
  setCookie,
  getToken,
  getRefreshToken,
  removeCookie,
} from "~~/helpers/authenticator";
import tokenProvider from "axios-token-interceptor";

export default defineNuxtPlugin((_app) => {
  const config = useRuntimeConfig();

  const instance = axios.create({
    baseURL: config.public.baseUrl,
    //timeout: 20000,
    headers: {
      Accept: "application/json",
    },
  });

  instance.interceptors.request.use(
    tokenProvider({
      getToken: () => getToken(),
    }) as any
  );

  instance.interceptors.response.use(
    (response) => response,
    function (error) {
      const originalRequest = error.config;
      if (
        error.response &&
        error.response.status === 401 &&
        originalRequest.url === `${config.baseUrl}oauth/token`
      ) {
        removeCookie();
        return Promise.reject(error);
      }
      if (
        error.response &&
        error.response.status === 401 &&
        !originalRequest._retry &&
        getRefreshToken()
      ) {
        originalRequest._retry = true;
        return instance
          .post("/oauth/token", {
            client_id: config.public.AUTH_CLIENT_ID,
            client_secret: config.public.AUTH_CLIENT_SECRET,
            scope: "",
            refresh_token: getRefreshToken(),
            grant_type: "refresh_token",
          })
          .then((res) => {
            if (res.status === 200 && res.data.access_token) {
              setCookie(res.data);
              instance.defaults.headers.common[
                "Authorization"
              ] = `Bearer ${res.data.access_token}`;
              return instance(originalRequest);
            }
          });
      }
      return Promise.reject(error);
    }
  );

  return {
    provide: {
      shopApi: instance,
    },
  };
});
