var pageination='<script type="text/x-template" id="pageination">\n' +
    '    <div class="pageination_align">\n' +
    '        <div class="pageination" v-if="pageinationTotal">\n' +
    '            <div @click="pageUp(0)" class="pagination_page" :class="startDisabled?\'disabled\':\'\'">首页</div>\n' +
    '            <div @click="pageUp(1)" class="pagination_page" :class="startDisabled?\'disabled\':\'\'">上一页</div>\n' +
    '            <div class="pagination_page" :class="item==pageinationCurrentPage?\'pagination_page_active\':\'\'"\n' +
    '                 v-for="item in pageinationLength" @click="jump(item)">\n' +
    '                {{item}}\n' +
    '            </div>\n' +
    '            <div @click="pageDown(1)" class="pagination_page" :class="endDisabled?\'disabled\':\'\'">下一页</div>\n' +
    '            <div @click="pageDown(0)" class="pagination_page pagination_page_right" :class="endDisabled?\'disabled\':\'\'">\n' +
    '                尾页\n' +
    '            </div>\n' +
    '        </div>\n' +
    '    </div>\n' +
    '</script>'
var dom = document.querySelector("body");
dom.innerHTML += pageination;
Vue.component('pageination', {
    props: ['total', 'size', 'page', 'changge', 'isUrl'],
    template: '#pageination',
    data() {
        return {
            pageinationTotal: this.total,//总条目数
            pageinationSize: this.size ? this.size : 10,//每页显示条目个数
            pageinationLength: [],
            pageinationCurrentPage: this.page ? this.page : 1,//当前页数默认1
            pageinationPage: 0,//可分页数
            startDisabled: true,//是否可以点击首页上一页
            endDisabled: true,//是否可以点击尾页下一页
            pageChangge: this.changge,//修改方法
            pageIsUrl: this.isUrl ? true : false,//是否开启修改url
        }
    },
    methods: {
        jump(item) {
            this.pageinationCurrentPage = item;
            this.pagers();
            this.pageChangge(this.pageinationCurrentPage);
        },//跳转页码
        pagers() {
            //可分页数
            this.pageinationPage = Math.ceil(this.pageinationTotal / this.pageinationSize);
            //url修改
            if (this.pageIsUrl) {
                this.$router.replace({
                    path: this.$route.path,
                    query: {
                        page: this.pageinationCurrentPage,
                    }
                });
            }
            //是否可以点击上一页首页
            this.startDisabled = this.pageinationCurrentPage != 1 ? false : true;
            //是否可以点击下一页尾页

            this.endDisabled = this.pageinationCurrentPage != this.pageinationPage ? false : true;
            if (this.pageinationPage == 0) this.endDisabled = true;
            //重置
            this.pageinationLength = [];
            //开始页码1
            let start = this.pageinationCurrentPage - 4 > 1 ? this.pageinationCurrentPage - 4 : 1;
            //当前页码减去开始页码得到差
            let interval = this.pageinationCurrentPage - start;
            //最多9个页码，总页码减去interval 得到end要显示的数量+
            let end = (9 - interval) < this.pageinationPage ? (9 - interval) : this.pageinationPage;
            //最末页码减开始页码小于8
            if ((end - start) != 8) {
                //最末页码加上与不足9页的数量，数量不得大于总页数
                end = end + (8 - (end - start)) < this.pageinationPage ? end + (8 - (end - start)) : this.pageinationPage;
                //最末页码加上但是还不够9页，进行开始页码追加，开始页码不得小于1
                if ((end - start) != 8) {
                    start = (end - 8) > 1 ? (end - 8) : 1;
                }
            }
            for (let i = start; i <= end; i++) {
                this.pageinationLength.push(i);
            }
        },//计算分页显示的数字
        pageUp(state) {
            if (this.startDisabled) return;
            if (this.pageinationCurrentPage - 1 != 0 && state == 1) {
                this.jump(this.pageinationCurrentPage - 1);
            } else {
                this.jump(1);
            }
        },//上一页跟首页 state=1是上一页 state=0是首页
        pageDown(state) {
            if (this.endDisabled) return;
            if (this.pageinationCurrentPage + 1 != this.pageinationPage && state == 1) {
                this.jump(this.pageinationCurrentPage + 1);
            } else {
                this.jump(this.pageinationPage);
            }
        },// state=1是下一页 state=0是尾页
        pageCurrentChange() {
            this.pageChangge(this.pageinationCurrentPage);
            this.pagers();
        }
    },
    created() {
        this.pageCurrentChange();
    },
    watch: {
        total: function (val, oldVal) {
            this.pageinationTotal = val;
            this.pagers();
        },
        page: function (val, oldVal) {
            this.pageinationCurrentPage = val;
            this.pagers();
        }
    }
})

