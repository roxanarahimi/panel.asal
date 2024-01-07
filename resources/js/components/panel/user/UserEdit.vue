<template>
    <transition name="route" mode="out-in" appear>
        <section>
            <h3 class="mb-5">ویرایش کاربر</h3>

            <div class="row mt-3">
                <div class="col-12 mb-3">
                    <div v-if="isDefined" class="card">
                        <div class="card-body">
                            <form id="editForm">
                                <div v-if="type == 'legal'" class="row">
                                    <div class="col-12 mb-3">
                                        <label class="form-label">تصویر آخرین روزنامه رسمی شرکت</label><br/>
                                        <image-cropper :name="1" :src="data.image1" caption=""
                                                       :hasCaption="hasCaption" :isRequired="imgRequired"
                                                       :aspect="aspect"/>
                                        <div id="image1Help" class="form-text error"></div>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">تصویر اساس نامه شرکت</label><br/>
                                        <image-cropper :name="2" :src="data.image2" caption=""
                                                       :hasCaption="hasCaption" :isRequired="imgRequired"
                                                       :aspect="aspect"/>
                                        <div id="image2Help" class="form-text error"></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="type" class="form-label">نوع کاربر</label>
                                        <select @change="updateType" class="form-select" id="type"
                                                aria-describedby="typeHelp" aria-label="type" required="">
                                            <option :selected="data.type === 'حقیقی'" value="real">حقیقی</option>
                                            <option :selected="data.type === 'حقوقی'" value="legal">حقوقی</option>
                                        </select>
                                        <div id="typeHelp" class="form-text error"></div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="name" class="form-label">نام</label>
                                        <input id="name" type="text" :class="{hasError: errors.name}" :value="data.name"
                                               class="form-control" aria-describedby="nameHelp" required>
                                        <div id="nameHelp" class="form-text error"></div>
                                        <p class="form-text error m-0" v-for="e in errors.name">{{ e }}</p>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="mobile" class="form-label">موبایل</label>
                                        <input id="mobile" type="number" :class="{hasError: errors.mobile}"
                                               :value="data.mobile" class="form-control" aria-describedby="mobileHelp"
                                               required>
                                        <div id="mobileHelp" class="form-text error"></div>
                                        <p class="form-text error m-0" v-for="e in errors.mobile">{{ e }}</p>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label  v-if="type == 'real'" for="national_code" class="form-label">کد ملی</label>
                                        <label  v-if="type == 'legal'" for="national_code" class="form-label">شناسه ملی شرکت</label>
                                        <input id="national_code" type="number"
                                               :class="{hasError: errors.national_code}" :value="data.national_code"
                                               class="form-control" aria-describedby="national_codeHelp" required>
                                        <div id="national_codeHelp" class="form-text error"></div>
                                        <p class="form-text error m-0" v-for="e in errors.national_code">{{ e }}</p>
                                    </div>

                                    <div v-if="type == 'legal'" class="col-md-3 mb-3">
                                        <label for="registration_number" class="form-label">شماره ثبت شرکت</label>
                                        <input id="registration_number" type="number" :class="{hasError: errors.registration_number}"
                                               :value="data.registration_number" class="form-control" name="registration_number"
                                               aria-describedby="registration_numberHelp" required>
                                        <div id="registration_numberHelp" class="form-text error"></div>
                                        <p class="form-text error m-0" v-for="e in errors.registration_number">{{ e }}</p>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="operator" class="form-label">نام اوپراتور</label>
                                        <input id="operator" type="text" :class="{hasError: errors.operator}" :value="data.operator"
                                               class="form-control" aria-describedby="operatorHelp" required>
                                        <div id="operatorHelp" class="form-text error"></div>
                                        <p class="form-text error m-0" v-for="e in errors.operator">{{ e }}</p>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="phone" class="form-label">تلفن</label>
                                        <input id="phone" type="number" :class="{hasError: errors.phone}"
                                               :value="data.phone" class="form-control" name="user_phone_"
                                               aria-describedby="phoneHelp" required>
                                        <div id="phoneHelp" class="form-text error"></div>
                                        <p class="form-text error m-0" v-for="e in errors.phone">{{ e }}</p>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="province_id" class="form-label">استان</label>
                                        <select @change="updateCities" class="form-select" id="province_id" aria-describedby="province_idHelp" aria-label="province_id" required="">
                                            <option v-for="item in provinces" :selected="data.city.province_id == item.id" :value="item.id">{{ item.title }}</option>
                                        </select>
                                        <div id="provinceHelp" class="form-text error"></div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="city_id" class="form-label">شهر</label>
                                        <select class="form-select" id="city_id" aria-describedby="city_idHelp" aria-label="city_id" required="">
                                            <option v-for="item in cities" :selected="data.city.id == item.id" :value="item.id">{{ item.title }}</option>
                                        </select>
                                        <div id="city_idHelp" class="form-text error"></div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="postal_code" class="form-label">کد پستی</label>
                                        <input id="postal_code" type="number" :class="{hasError: errors.postal_code}"
                                               :value="data.postal_code" class="form-control" aria-describedby="postal_codeHelp"
                                               required>
                                        <div id="postal_codeHelp" class="form-text error"></div>
                                        <p class="form-text error m-0" v-for="e in errors.mobile">{{ e }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="address" class="form-label">آدرس</label>
                                        <input id="address" type="text" :class="{hasError: errors.address}" :value="data.address"
                                               class="form-control" aria-describedby="addressHelp" required>
                                        <div id="addressHelp" class="form-text error"></div>
                                        <p class="form-text error m-0" v-for="e in errors.address">{{ e }}</p>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <button class="btn btn-primary" @click.prevent="updateInfo" type="submit">
                                            <!--                                        <button class = "btn btn-primary" type = "submit">-->
                                            ویرایش
                                        </button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </transition>


</template>

<script>
import ImageCropper from '../../components/ImageCropper';
import App from '../App';

export default {
    components: {ImageCropper},
    data() {
        return {
            id: this.$route.params.id,
            data: {},
            categories: [],
            errors: [],
            image_codes: [],
            image_names: [],
            imgRequired: false,
            hasCaption: false,
            aspect: 16 / 9,
            isDefined: false,
            enableClick: true,
            features: [],
            progress: 0,
            type: '',
            provinces: [],
            cities: [],
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
                .then(() => {
                    this.isDefined = true;
                })
                .then(() => {
                    this.updateType();
                    this.loadProvinces();
                })
                .catch();


        },
        async updateInfo() {
            await App.methods.checkToken();
            this.errors = [];
            let emptyFieldsCount = 0;
            let req = document.querySelectorAll('[required]');
            req.forEach((element) => {
                if (element.value === "") {
                    element.classList.add('hasError');
                    element.nextSibling.innerHTML = "فیلد اجباری";
                    emptyFieldsCount++;
                } else {
                    element.classList.remove('hasError');
                    element.nextSibling.innerHTML = "";
                }
            });

            if (emptyFieldsCount === 0) {

                let info = {
                    name: document.querySelector('#name').value,
                    national_code: document.querySelector('#national_code').value,
                    phone: document.querySelector('#phone').value,
                    mobile: document.querySelector('#mobile').value,
                    city_id: selectedProvince.value.id,
                    address: document.querySelector('#address').value,
                    postal_code: document.querySelector('#postal_code').value,
                    scope: 'user',
                    type: this.type,
                };
                if (this.type == 'legal') {
                    info['registration_number'] = document.querySelector('#registration_number').value;
                    info['operator'] = document.querySelector('#operator').value;
                    info['img1'] = document.querySelector('#img1').value;
                    info['img2'] = document.querySelector('#img2').value;
                }
                await axios.post('/api/panel/user/' + this.$route.params.id,info)
                    .then((response) => {
                        if (response.status === 200) {
                            setTimeout(() => {
                                this.$router.push({path: '/panel/user/' + this.id});
                                // this.$router.push({path: '/panel/users' });
                            }, 1000);
                        }
                    })
                    .catch((error) => {
                        if (error.status === 422) {
                            let errorList = Array(error.response.data);
                            for (var i = 0; i < errorList.length; i++) {
                                // console.log('i', errorList[i]);
                                this.errors = errorList[i];
                            }
                        } else if (error.status === 500) {
                            if (error.data.message.includes("SQLSTATE")) {
                                console.error('خطای پایگاه داده');

                                async function showAlertSql() {
                                    setTimeout(() => {
                                        alert(error.response.data.message);
                                    }, 200);
                                }

                                showAlertSql();
                            } else {
                                async function showAlert500() {
                                    setTimeout(() => {
                                        alert(error.message + ' '
                                            + error.response.data.message);
                                    }, 200);
                                }

                                showAlert500();
                            }

                        } else {
                            async function showAlert() {
                                await document.querySelector('.progress-bar').classList.add('bg-danger');
                                setTimeout(() => {
                                    alert(error.message);
                                }, 200);
                            }

                            showAlert();

                        }
                    });
            }

        },

        loadProvinces(){
            axios.get('/api/province')
            .then((response)=>{
                this.provinces = response.data;
            })
            .then(()=>{
                this.updateCities();
            })
           .catch((error)=>{ console.error(error)});
        },
        updateType() {
            this.type = document.querySelector('#type').value;

        },
        updateCities() {
            let province = this.provinces.find((element) => element.id == document.querySelector('#province_id').value);
            this.cities = province.cities;
        }


    }
}
</script>
<style scoped>
span i {
    cursor: pointer;
}


</style>
