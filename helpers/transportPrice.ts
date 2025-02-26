function transportPrice(packages: any) {
  if (!packages) {
    return 0;
  }
  let price = 0;
  price = packages.transport_groups.reduce(
    (prev: any, item: any) => prev + parseFloat(item.transport_price),
    price
  );
  price = packages.packages.reduce(
    (prev: any, item: any) => prev + parseFloat(item.price),
    price
  );
  return price;
}
export default transportPrice;
