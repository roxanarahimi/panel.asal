<template>
    <transition name = "route" mode = "out-in" appear>
        <section class = "" v-if = "data.id">
            <div class = "d-inline-block">
                <h3 class = "mb-4 fw-bold d-block">اطلاعات کاربر({{ data.type }})</h3>
            </div>
            <!--            <router-link :to = "'/panel/edit/user/'+data.id" class = "text-dark">-->
            <!--                <span title = "ویرایش محصول" class = "mx-3 p-2 d-inline-block align-middle bg-dark text-light rounded-circle">-->
            <!--                    <i class = "bi bi-pencil p-0 edit-pen"></i>-->
            <!--                </span>-->
            <!--            </router-link>-->

            <div class = "row mt-3">
                <div class = "col-md-6 col-lg-4 col-xxl-3 mb-3 h-100">
                    <div class = "card h-100">
                        <!-- <images  class="card-images-top" alt="..."> -->
                        <div class = "card-body text-center pt-3">
                            <!-- <i class="bi bi-person-circle"></i> -->
                            <i class = "bi bi-person-circle text-muted " style = "font-size: 60px"></i>

                            <p class = "h5 card-title fw-bold mt-2 mb-2">{{data.name }}</p>
                            <p class = "text-muted d-block my-0">{{ data.mobile }}</p>
                            <router-link :to = "'/panel/edit/user/'+data.id" class = "btn btn-sm btn-primary mt-2">
                                ویرایش
                            </router-link>
                        </div>
                    </div>
                </div>

                <div class = "col-xxl-9 mb-3 row ">
                    <div class = "col-12 mb-3">
                        <div class = "card">
                            <div class = "card-body">
                                <table class = "table">
                                    <thead>
                                    <tr>
                                        <th>استان</th>
                                        <th>شهر</th>
                                        <th>کد پستی</th>
                                        <th>تلفن</th>
                                        <th>آدرس</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    <tr>
                                        <td>{{ data.city.province.title}}</td>
                                        <td>{{ data.city.title}}</td>
                                        <td>{{ data.postal_code }}</td>
                                        <td>{{ data.phone }}</td>
                                        <td>{{ data.address }}</td>
                                    </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div v-if="data.typeEn == 'legal'" class = "col-12 mb-3">
                        <div class = "card">
                            <div class = "card-body">
                                <table class = "table">
                                    <thead>
                                    <tr>
                                        <th>نام شرکت</th>
                                        <th>شناسه ملی</th>
                                        <th>شماره ثبت</th>
                                        <th>نام اوپراتور</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    <tr>
                                        <td>{{ data.name}}</td>
                                        <td>{{ data.national_code}}</td>
                                        <td>{{ data.registration_number }}</td>
                                        <td>{{ data.operator }}</td>
                                    </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class = "row">
                <div class = "mb-3 h-100">
                    <div class = "col-12 mb-3">
                        <div v-if = "data.allOrders.length" class = "card">
                            <div class = "card-body">
                                <table class = "table">
                                    <thead>
                                    <tr>
                                        <th class = "d-none d-md-table-cell" scope = "col"></th>
                                        <th scope = "col">کد سفارش</th>
                                        <th scope = "col">مبلغ</th>
                                        <th scope = "col">پرداخت</th>
                                        <th scope = "col">وضعیت</th>
                                        <th class = "d-none d-md-table-cell" scope = "col">تاریخ ثبت</th>
                                        <th scope = "col"></th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    <tr :id = "'row_'+data.id" v-for = "(item, index) in data.allOrders" :key = "data.id" :data-index = "index">
                                        <td class = "d-none d-md-table-cell" scope = "row">{{ index + 1 }}</td>

                                        <td>
                                            <router-link :to = "'/panel/order/'+item.id">{{ item.code }}</router-link>
                                        </td>


                                        <td>{{ item.amount }}</td>
                                        <td>
                                            {{ item.payment }}
                                        </td>

                                        <td class = "  pt-0">
                                            <p class = "m-0 mb-1">{{ item.status}}</p>
                                            <div class = "progress order_status " style = "height: 5px">
                                                <div
                                                    :class = "'progress-bar order_status rounded ' + item.color"
                                                    role = "progressbar"
                                                    :style = "'width: '+ item.percent+'%'"
                                                    :aria-valuenow = "item.percent"
                                                    aria-valuemin = "0"
                                                    aria-valuemax = "100"></div>
                                            </div>
                                        </td>
                                        <td class = "d-none d-md-table-cell date_cell">{{ item.created_at }}</td>

                                        <td>
                                            <span role = "button" data-bs-toggle = "dropdown" aria-expanded = "false"><i class = "bi bi-three-dots-vertical"></i></span>
                                            <ul class = "dropdown-menu" aria-labelledby = "navbarScrollingDropdown">
                                                <!--                                            <li>-->
                                                <!--                                                <a class = "dropdown-item" style = "text-align: right !important" href = "#">مشاهده</a>-->
                                                <!--                                            </li>-->
                                                <!--                                             <li>-->
                                                <!--                                                <a class = "dropdown-item" style = "text-align: right !important" href = "#">ویرایش</a>-->
                                                <!--                                            </li>-->
                                                <li>
                                                    <router-link :to = "'/panel/order/'+item.id" class = "dropdown-item" style = "text-align: right !important">
                                                        مشاهده
                                                    </router-link>
                                                    <router-link :to = "'/panel/edit/order/'+item.id" class = "dropdown-item" style = "text-align: right !important">
                                                        ویرایش
                                                    </router-link>
                                                    <a class = "dropdown-item" @click = "showDeleteModal(item.id)" style = "text-align: right !important" data-bs-toggle = "modal" data-bs-target = "#exampleModal">حذف</a>
                                                </li>
                                                <!--                                <li><hr class="dropdown-divider"></li>-->
                                                <!--                                <li><a class="dropdown-item" href="#">Something else here</a></li>-->
                                            </ul>
                                        </td>
                                    </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div v-else><p class = "fw-bold">هیچ سفارشی موجود نیست</p></div>
                    </div>
                </div>
            </div>

        </section>
        <div v-else><p class = "fw-bold">کاربر موجود نیست</p></div>

    </transition>


</template>

<script>
    import App from '../App';

    export default {
        data: function () {
            return {
                data: {},
                id: this.$route.params.id,
                features: [],
            }
        },
        mounted() {
            this.loadData();
        },
        methods: {
            async loadData() {
                await App.methods.checkToken();
                await axios.get('/api/panel/user/' + this.id)
                    .then((response) => {
                        this.data = response.data;
                    })
                    .catch();
            },

        }
    }
</script>
<style>
    #content p {
        text-align: justify !important;
        line-height: 30px !important;
        padding-left: 10px;

    }

    #content figure {
        text-align: center !important;
        display: block !important;
    }

    #content figure figcaption {
        text-align: center !important;
    }

    .index_image .label {
        display: block;
        font-size: 20px;
        margin: -50px 20px 50px 20px;
    }

    #content figure img {
        max-width: 100%;
    }

    /*.index_image span{*/
    /*    display: inline-block;*/
    /*    right:20px;*/
    /*    bottom: 20px;*/
    /*}*/
    .edit-pen {
        font-size: 12px;
        margin: 0 6px !important;
    }
</style>
