class Cart {
  products: any[] = [];

  init() {
    if (typeof window !== "undefined") {
      try {
        const cart = localStorage.getItem("cart");
        if (cart) {
          const data = JSON.parse(cart);

          if (data) {
            this.products = data;
          }
        }
      } catch (e) {
        console.error(e);
      }
    }
  }

  getEditedCart() {
    return localStorage.getItem("editedCart") ?? false;
  }

  addToCart(product: any, amount?: number | string) {
    this.init();
    const idx = this.getIdxByProductId(product.id);
    if (idx >= 0) {
      this.incraseAmount(idx, amount as any);
    } else {
      this.products.push({
        amount: parseInt(amount as any),
        ...product,
      });
      this.save();
    }

    localStorage.setItem("editedCart", "true");
  }

  exists(id: number) {
    return this.getIdxByProductId(id) >= 0;
  }

  getIdxByProductId(id: number) {
    return this.products.findIndex((product: any) => product.id === id);
  }

  removeFromCart(id: number) {
    const idx = this.getIdxByProductId(id);

    if (idx >= 0) {
      this.products.splice(idx, 1);
      this.products = [...this.products];

      this.save();
    }
  }

  removeAllFromCart() {
    localStorage.removeItem("editedCart");

    this.products = [];
    this.save();
  }

  toggleProduct(product: any) {
    if (this.exists(product.id)) {
      this.removeFromCart(product.id);
    } else {
      this.addToCart(product);
    }
  }

  incraseAmount(idx: number, increment: number) {
    this.products[idx].amount = this.products[idx].amount + increment;
    this.save();
  }

  decraseAmount(idx: number) {
    if (this.products[idx].amount > 1) {
      this.products[idx].amount = this.products[idx].amount - 1;
      this.save();
    }
  }

  changeAmount(idx: number, newAmount: number | string) {
    this.products[idx].amount = parseInt(newAmount as any);
    this.save();
  }

  idsWithQuantity() {
    var selectedProducts: any[] = [];
    this.products.forEach((product) => {
      if (product == null) {
        return;
      }
      var item = {
        symbol: product.symbol,
        amount: product.amount,
        recalculate: product.recalculate,
      };
      selectedProducts.push(item);
    });
    return selectedProducts;
  }

  nettoPrice() {
    let totalPrice = 0;
    this.products.map((product) => {
      totalPrice += product.amount * product.net_selling_price_commercial_unit;
    });
    return parseFloat(totalPrice as any).toFixed(2);
  }

  grossPrice() {
    let totalPrice = 0;
    this.products.map((product) => {
      totalPrice += product.amount * product.gross_price_of_packing;
    });

    return parseFloat(totalPrice as any).toFixed(2);
  }

  totalWeight() {
    let totalWeight = 0;
    this.products.map((product) => {
      totalWeight += product.weight_trade_unit * product.amount;
    });
    return parseFloat(totalWeight as any).toFixed(2);
  }

  save() {
    try {
      localStorage.setItem("cart", JSON.stringify(this.products));
    } catch (ex) {
      console.log(ex);
    }
  }

  toJSON() {
    return { ...this };
  }
}

export default Cart;
