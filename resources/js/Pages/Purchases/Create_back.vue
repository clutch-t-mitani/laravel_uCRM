<script setup>
import { getToday } from '@/common.js'
import { reactive, onMounted, ref, computed } from 'vue';
import { Inertia } from '@inertiajs/inertia';

const itemList = ref([]);

// ページ読み込んですぐ実行
onMounted(() => {
    form.date = getToday()
    props.items.forEach( item => { // 配列を1つずつ処理
        itemList.value.push({ // 配列に1つずつ追加
            id: item.id, name: item.name, price: item.price, quantity: 0
        })
    })
})

const props = defineProps({
    customers: Array,
    items: Array
})

const form = reactive({
    date: null,
    customer_id: null,
    status: true,
    items: []
})

const totalPrice = computed(() => {
    let total = 0;
    itemList.value.forEach( item => {
        total += item.price * item.quantity
    })
    return total.toLocaleString();
})

const quantity = [ "0", "1", "2", "3", "4", "5", "6", "7", "8", "9"] // option用

const storePurchase = () => {
    itemList.value.forEach( item => {
        if (item.quantity > 0) {
            form.items.push({
                id: item.id,
                quantity: item.quantity
            })
        }
    });


    Inertia.post(route('purchases.store'), form)
}

</script>

<template>
    <form @submit.prevent="storePurchase">

        日付<br>
        <input type="date" name="date" v-model="form.date">
        <br>
        会員名<br>
        <select name="customer" v-model="form.customer_id">
            <option v-for="customer in customers" :value="customer.id" :key="customer.id">
                {{ customer.id }} : {{ customer.name }}
            </option>
        </select>
        <br>
        商品・サービス<br>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>商品名</th>
                    <th>金額</th>
                    <th>数量</th>
                    <th>小計</th>
                </tr>
            </thead>
            <tbody>
            <tr v-for="item in itemList" >
                <td>{{ item.id }}</td>
                <td>{{ item.name }}</td>
                <td>{{ '¥' + item.price.toLocaleString() }}</td>
                <td>
                    <select name="quantity" v-model="item.quantity">
                        <option v-for="q in quantity" :value="q">{{ q }}</option>
                    </select>
                </td>
                <td>{{ '¥' + (item.price * item.quantity).toLocaleString() }}</td>
            </tr>
            </tbody>
        </table>
        <br>
        <span>合計: ¥<span class="price">{{ totalPrice }}</span></span>
        <br>
        <button>登録する</button>
    </form>
</template>

