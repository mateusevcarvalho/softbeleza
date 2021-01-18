import moment from "moment";

const datas = [];

for (let i = 0; i <= 11; i++) {
    datas.push({value: i, name: moment('01', 'MM').month(i).format('MMMM')});
}

export default datas;
