const buildImgRoute = (path: string, baseUrl: string) =>
  path ? baseUrl + path : defaultImgSrc;

export const defaultImgSrc = "https://via.placeholder.com/185x150";

export default buildImgRoute;
