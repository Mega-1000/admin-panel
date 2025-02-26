import { defaultImgSrc } from "~~/helpers/buildImgRoute";

export default defineNuxtPlugin(() => {
  const baseUrl = useRuntimeConfig().public.baseUrl;

  return {
    provide: {
      buildImgRoute: (path: string) => (path ? baseUrl + path : defaultImgSrc),
    },
  };
});
