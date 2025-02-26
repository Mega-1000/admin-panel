const findActiveMenu = (items: any[], active: number | string): any => {
  const find = (elem: any) => {
    if (String(elem.id) === String(active)) {
      return elem;
    }

    let result = false;

    if (elem.children) {
      elem.children.some((elem: any) => {
        result = find(elem);

        return result;
      });
    }

    return result;
  };

  let result = false;

  items.some((elem) => {
    result = find(elem);

    return result;
  });

  return result;
};

export default findActiveMenu;
