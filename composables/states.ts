import Cart from "~~/utils/Cart";

export const usePhone = () => useState<string | null>("phone", () => null);
export const useCart = () => useState("cart", () => "");
export const useCurrentItem = () => useState<any>("currentItem", () => null);
export const useSelectedMediaId = () =>
  useState<any>("selectedMediaId", () => null);
export const useProductsCart = () => useState("productsCart", () => new Cart());
export const useProductAmount = () => useState("productAmount", () => 1);
export const useUserToken = () => useState("userToken", () => "");
export const useUser = () => useState<any>("user", () => null);
