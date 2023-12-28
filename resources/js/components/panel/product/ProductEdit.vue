<template>
    <transition name="route" mode="out-in" appear>
        <section>
            <h3 class="mb-5">ویرایش محصول</h3>

            <div class="row mt-3">
                <div class="col-12 mb-3">
                    <div class="card" v-if="isDefined">
                        <div class="card-body">
                            <form id="editForm">
                                <!--                                <div class="row">-->
                                <!--                                    <div class="col-12 mb-3">-->
                                <!--                                        <label class="form-label">تصویر</label><br/>-->
                                <!--                                        <image-cropper name="" caption="" :hasCaption="hasCaption"-->
                                <!--                                                   :isPng="isPng"    :isRequired="imgRequired" :aspect="aspect" :src="data.image"/>-->
                                <!--                                        <div id="imageHelp" class="form-text error"></div>-->
                                <!--                                    </div>-->
                                <!--                                </div>-->

                                <div class="row">
                                    <div class="col-md-4 col-lg-4 mb-3">
                                        <label for="category" class="form-label">دسته</label>
                                        <select @change="updateData" class="form-select" id="category"
                                                aria-describedby="categoryHelp"
                                                aria-label="category" >
                                            <option value=""></option>
                                            <option :selected="data.category.id == category.id"
                                                    v-for="category in categories" :key="category.id"
                                                    :value="category.id">
                                                {{ category.title }}
                                            </option>
                                        </select>
                                        <div id="categoryHelp" class="form-text error"></div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="title" class="form-label">عنوان</label>
                                        <input @input="updateData" type="text" :class="{hasError: errors.title}"
                                               class="form-control"
                                               id="title" :value="data.title" aria-describedby="titleHelp" required>
                                        <div id="titleHelp" class="form-text error"></div>
                                        <p class="form-text error m-0" v-for="e in errors.title">{{ e }}</p>

                                    </div>

                                    <div class="col-md-4 col-lg-3 mb-3">
                                        <label for="price" class="form-label">قیمت (ریال)</label>
                                        <input type="number" min="1000" :class="{hasError: errors.price}"
                                               class="form-control text-start" id="price" :value="data.price" required>
                                        <div id="priceHelp" class="form-text error"></div>
                                        <p class="form-text error m-0" v-for="e in errors.price">{{ e }}</p>

                                    </div>
                                    <div class="col-md-4 col-lg-1 mb-3">
                                        <label for="off" class="form-label">%تخفیف</label>
                                        <input type="number" :class="{hasError: errors.off}"
                                               class="form-control text-start" id="off" :value="data.off">
                                        <div id="offHelp" class="form-text error"></div>
                                        <p class="form-text error m-0" v-for="e in errors.off">{{ e }}</p>

                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label" for="text">متن</label>
                                        <textarea @input="watchTextAreas" :class="{hasError: errors.text}"
                                                  aria-describedby="textHelp" class="form-control text-start" id="text">{{ data.text}}</textarea>
                                        <div id="textHelp" class="form-text error"></div>
                                        <p class="form-text error m-0" v-for="e in errors.text">{{ e }}</p>

                                    </div>

                                    <div class="col-md-12 mb-3" id="sizes">
                                        <div>
                                            <label class="form-label mb-1 align-middle">سایز و رنگ</label>
                                            <span @click="addSize" class="px-3 d-inline-block align-middle"><i
                                                class="bi bi-plus-circle-fill p-0 mt-2 m-0" style="font-size: 15px"></i></span>
                                        </div>

                                        <div v-for="(item, index) in sizes" :key="index" id="sizeSection"
                                             class="row sizeElement">
                                            <input type="hidden" name="size_id" :value="item.id || null">
                                            <div class="col-6 col-md-2 mb-3">
                                                <input type="text" name="size" class="form-control" @input="updateSizes"
                                                       :value="item.size" placeholder="سایز" required>
                                                <div class="form-text error"></div>
                                            </div>
                                            <div class="col-10 col-md-1 mb-3">
                                                <input type="number" name="stock" class="form-control" min="0" dir="ltr"
                                                       @input="updateSizes" :value="item.stock" placeholder="موجودی"
                                                       required>
                                                <div class="form-text error"></div>
                                            </div>
                                            <div class="col-1 mb-3 pt-2">
                                                <span @click="removeSize(index, item.id)"><i class="bi bi-x-circle-fill m-0 " style="font-size: 15px"></i></span>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <BtnSubmit @click.prevent="updateInfo">
                                            ویرایش
                                        </BtnSubmit>
                                        <!--                                        <button id="submit" class="btn btn-primary d-flex justify-content-between" @click.prevent="updateInfo" type="submit">-->
                                        <!--                                             ویرایش <loader-sm class="loader-sm d-none" />-->
                                        <!--                                        </button>-->
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
import BtnSubmit from "../../components/BtnSubmit";
import Multiselect from '@vueform/multiselect'


export default {
    components: {ImageCropper, App, BtnSubmit, Multiselect},
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
            aspect: false,
            isPng: true,
            isDefined: false,
            enableClick: true,
            features: [],
            sizes: [],
            removedSizes: [],
            images: [],

            value: [],
            allProducts: []
        }
    },

    created() {
        this.loadCategories();
        this.loadProduct();
        this.loadProducts();
    },

    methods: {
        loadProduct() {

            axios.get('/api/panel/product/' + this.id)
                .then((response) => {
                    console.log(response.data);
                    this.data = response.data.product;
                    // if (this.data.features) {
                    //     for (let i = 0; i < JSON.parse(this.data.features).length; i++) {
                    //         this.features.push(JSON.parse(this.data.features)[i]);
                    //     }
                    // }
                    if (this.data.sizes && this.data.sizes.length) {
                        this.sizes = this.data.sizes;
                    }
                    if (this.data.images) {
                        this.images = this.data.images;
                    }
                })
                .then(() => {
                    this.isDefined = true;
                })
                .then(() => {
                    this.value = this.data.related_products;
                })
                .then(() => {
                    this.watchTextAreas();
                })
                .catch();
        },
        loadProducts() {

            axios.get('/api/panel/product?page=1&perPage=1000&search=')
                .then((response) => {
                    this.allProducts = response.data.data;
                    this.allProducts = this.allProducts.filter((item)=>item.id != this.id);
                })
                .catch()
        },
        loadCategories() {
            axios.get('/api/panel/category/product?page=1&perPage=100000')
                .then((response) => {
                    this.categories = response.data.data;
                })
                .catch();
        },
        updateInfo() {


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

                axios.post('/api/panel/product/' + this.$route.params.id,
                    {
                        // image: document.getElementById('Image_index_code').value,
                        // image: document.getElementById('Image__code').value,
                        id: this.$route.params.id,
                        title: document.getElementById('title').value,
                        product_category_id: document.getElementById('category').value,
                        text: document.getElementById('text').value,
                        sizes: this.sizes,
                        removedSizes: this.removedSizes
                    })
                    .then((response) => {
                        console.log('res', response);
                        if (response.status === 200) {
                            setTimeout(() => {
                                this.$router.push({path: '/panel/product/' + this.id});
                            }, 1000);
                        }
                    })
                    .catch((error) => {
                        document.querySelector('#submit').removeAttribute('disabled');
                        document.querySelector('.loader-sm').classList.add('d-none');

                        if (error.response.status === 422) {
                            let errorList = Array(error.response.data);
                            for (var i = 0; i < errorList.length; i++) {
                                this.errors = errorList[i];
                            }

                        } else if (error.response.status === 500) {
                            if (error.response.data.message.includes("SQLSTATE")) {
                                console.error('خطای پایگاه داده');

                                function showAlertSql() {
                                    setTimeout(() => {
                                        alert(error.response.data.message);
                                    }, 200);
                                }

                                showAlertSql();
                            } else {
                                function showAlert500() {
                                    setTimeout(() => {
                                        alert(error.message + ' '
                                            + error.response.data.message);
                                    }, 200);
                                }

                                showAlert500();
                            }

                        } else {
                            function showAlert() {
                                setTimeout(() => {
                                    alert(error.message);
                                }, 200);
                            }

                            showAlert();

                        }
                    });
            }
        },

        updateData() {
            this.data.title = document.getElementById('title').value;
            this.data.title_en = document.getElementById('title_en').value;
            this.data.flavor = document.getElementById('flavor').value;
            this.data.flavor_en = document.getElementById('flavor_en').value;
            this.data.text = document.getElementById('text').value;
            this.data.product_category_id = document.getElementById('category').value;
            this.data.color = document.getElementById('color').value;
            this.data.index = document.getElementById('index').value;
            this.data.link = document.getElementById('link').value;
        },
        watchTextAreas() {
            let txt = document.querySelector("#text");
            txt.setAttribute("style", "height:" + (txt.scrollHeight + 20) + "px;overflow-y:hidden;");
            txt.addEventListener("input", changeHeight, false);

            function changeHeight() {
                this.style.height = "auto";
                this.style.height = (this.scrollHeight) + "px";
            }
        },
        addSize() {

            this.sizes.push('{}');
        },
        removeSize(index,id) {
            if (id != null){this.removedSizes.push(id)}
            this.sizes.splice(index, 1);

        },
        async updateSizes() {
            let a = [];
            for (let i = 0; i < document.getElementsByName('size').length; i++) {
                await a.push({
                    "id": document.getElementsByName('size_id')[i].value.toString(),
                    "size": document.getElementsByName('size')[i].value.toString(),
                    "stock": document.getElementsByName('stock')[i].value,
                });
            }
            this.sizes = a;

        },

    }
}
</script>
<style src="@vueform/multiselect/themes/default.css"></style>

<style>
span i {
    cursor: pointer;
}

.en {
    direction: ltr !important;
    text-align: left !important;
}

.multiselect-tags-search{
    background-color: transparent !important;
}
.multiselect-tag{
    background-color: #0d6efd !important;
}
.multiselect.is-active
{
    box-shadow: none !important;
}
</style>
