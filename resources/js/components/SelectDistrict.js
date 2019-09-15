// 从刚刚安装的库中加载数据
const addressData = require('china-area-data/v3/data');

// 引入loadsh,loadsh 是一个实用工具库，提供了很多常用的方法
import _ from 'lodash';

//  注册一个名为 select-district 的 Vue 组件
Vue.component('select-district', {
    // 定义组件的属性
    props: {
        // 用来初始化省市区的值，在编辑的时候用到
        initValue: {
            type: Array, // 格式是数组
            default: () => ([]),
        }
    },

    // 定义了这个组件内的数据
    data() {
        return {
            provinces: addressData['86'], // 省份表
            cities: {}, // 城市列表
            districts: {},  // 地区列表
            provinceId: "", // 当前选中的省份
            cityId: "",     // 选中的市
            districtId: '', // 当前选中的区
        }
    },

    // 定义观察器，对应属性变更时会触发的观察器函数
    watch: {
        // 当选择的省变动时触发
        provinceId(newVal) {
            if (!newVal) {
                this.cities = {};
                this.cityId = '';
                return
            }

            // 将城市列表设为当前省下的城市
            this.cities = addressData[newVal];
            // 如果当前选中的城市不在当前省下，则将选中的城市清空
            if (!this.cities[this.cityId]) {
                this.cityId = '';
            }
        },
        // 当选择的市发生改变时触发
        cityId(newVal) {
            if (!newVal) {
                this.districts = {};
                this.districtId = '';
                return
            }

            // 将地区列表设置成当前城市下的地区
            this.districts = addressData[newVal];
            // 如果地区不在该城市下，清空
            if (!this.districts[this.districtId]) {
                this.districtId = ''
            }
        },

        // 当选择的区发生改变
        districtId() {
            // 触发一个名为 change 的 Vue 事件，事件的值就是当前选中的城市名称，格式为数组
            this.$emit('change', [this.provinces[this.provinceId], this.cities[this.cityId], this.districts[this.districtId]])
        }
    },

    // 组件初始化的时候调用这个方法
    created() {
        this.setFromValue(this.initValue)
    },

    methods: {
        setFromValue(value) {
            // 过滤掉空值
            value = _.filter(value);
            // 如果数组长度是0，则将其清空（由于定义了观察器，会联动触发将城市清空）
            if (value.length == 0) {
                this.provinceId = '';
                return;
            }

            // 从当前省份列表中找到与数组第一个元素同名的项索引
            const provinceId = _.findKey(this.provinces, o => o === value[0]);
            // 没找到，清空省份的值
            if (!provinceId) {
                this.provinceId = '';
                return
            }
            // 找到了将当前省份设置成相应的省
            this.provinceId = provinceId;

            // 从当前省份中查找城市的值
            const cityId = _.findKey(addressData[provinceId], o => o === value[1]);
            // 没找到，清空城市的值
            if (!cityId) {
                this.cityId = '';
                return
            }

            // 找到了，将当前城市设置成对应的ID
            this.cityId = cityId;
            // 由于观察器的租用，这个时候地区列表已经变成了对应城市的地区列表
            // 从当前地区列表找到与数组第三个元素同名的项的索引
            const districtId = _.findKey(addressData[cityId], o => o => value[2]);
            // 如果没找到，清空地区的值
            if (!districtId) {
                this.districtId = '';
                return
            }
            // 找到了就把当前地区设置成对应的值
            this.districtId = districtId
        }
    }

});
