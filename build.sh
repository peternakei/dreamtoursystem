cd ./FRONTEND
npm install
npm run build
cd ../BACKEND
rm -r ./public/assets
cp -r ../FRONTEND/dist/* ./public/

