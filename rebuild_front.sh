# nvm use 16
git pull
rm -rf node_modules
rm package-lock.json
rm yarn.lock
rm -rf .nuxt
npm cache clear -f
yarn install
yarn build

# Usuwamy katalog produkcyjny
#rm ../../www/front-page -R

# Wrzucamy skompulowany kod
#cp ../front-page-build ../../www/front-page -R

pm2 update
