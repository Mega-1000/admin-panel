const image = (url: string, base: string) => {
  if (/https?/.test(url)) {
    return url;
  }

  return base + url;
};

export default image;
