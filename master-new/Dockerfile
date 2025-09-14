FROM node:latest

WORKDIR /app

COPY package*.json ./

RUN npm install
RUN npm install --save datatables.net-dt
RUN npm install datatables.net-select --save
RUN npm install bootstrap@5 --save
RUN npm install axios
RUN npm install vue-router@4

RUN npm install jquery --save

RUN npm install datatables.net-bs5 --save
RUN npm install bootstrap-datepicker --save

RUN npm install bootstrap-icons
RUN npm install jquery.maskedinput

RUN npm install apexcharts --save

COPY . .

RUN npm run build

EXPOSE 80
CMD ["npm", "start"]
