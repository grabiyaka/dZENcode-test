<template>
<div>
    <h1 id="title">Home</h1>
    <div v-if="$store.getters.getUser?.id">
        Pleace write your comment
        <quill-editor ref="quillComponent"></quill-editor>
    </div>

    <div v-if="$store.getters.getPosts.length" id="posts">
        <h2>Cool posts:</h2>
        <CommentTree :posts="$store.getters.getPosts"></CommentTree>

        <!-- Пагинация -->
        <ul class="pagination">
            <li v-if="paginate.currentPage > 1" :class="{ 'active-prev': paginate.currentPage > 2 }" class="page-item">
                <button @click="paginate.currentPage = paginate.currentPage - 1" class="page-link">Предыдущая</button>
            </li>
            <li v-for="page in displayedPages" :key="page" :class="{ 'active': page === paginate.currentPage, 'active-prev': page === paginate.currentPage - 1, 'active-next': page === paginate.currentPage + 1 }" class="page-item">
                <button @click="paginate.currentPage = page" class="page-link">{{ page }}</button>
            </li>
            <li v-if="paginate.currentPage < paginate.totalPages" :class="{ 'active-next': paginate.currentPage + 1 === paginate.currentPage + 1 }" class="page-item">
                <button @click="paginate.currentPage = paginate.currentPage + 1" class="page-link">Следующая</button>
            </li>
        </ul>
    </div>
</div>
</template>

<script>
import axios from 'axios';
import QuillEditor from "./../layouts/QuillEditor.vue";
import CommentTree from './../layouts/CommentTree.vue';

export default {
    data() {
        return {
            post: {
                content: ''
            },
            posts: [],
            reply: '',
            paginate: {
                currentPage: 1,
                totalPages: 1,
            },
        };
    },
    components: {
        QuillEditor,
        CommentTree,
    },
    computed: {
        displayedPages() {
            const maxDisplayedPages = 9;
            const currentPage = this.paginate.currentPage;
            const totalPages = this.paginate.totalPages;

            if (totalPages <= maxDisplayedPages) {
                return Array.from({
                    length: totalPages
                }, (_, index) => index + 1);
            }

            const middle = Math.ceil(maxDisplayedPages / 2);
            let start = Math.max(1, currentPage - middle + 1);
            let end = Math.min(totalPages, start + maxDisplayedPages - 1);

            if (end - start + 1 < maxDisplayedPages) {
                end = totalPages;
                start = Math.max(1, end - maxDisplayedPages + 1);
            }

            return Array.from({
                length: end - start + 1
            }, (_, index) => start + index);
        }

    },
    methods: {
        async getPosts() {
            try {
                const response = await axios.get(`/api/posts?page=${this.paginate.currentPage}`);
                this.$store.commit('setPosts', response.data.data);
                this.paginate.totalPages = response.data.last_page;

            } catch (error) {
                console.log(error);
            }
        },
        scrollToBlock(blockId) {
            const element = document.getElementById(blockId);

            if (element) {
                element.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        },
    },
    mounted() {
        this.getPosts();
        this.$store.dispatch('fetchUser');
    },
    watch: {
        'paginate.currentPage': function (newPage, oldPage) {
            if (newPage !== oldPage) {
                this.getPosts();
                this.scrollToBlock('posts');
            }
        },
    },

};
</script>

<style lang="scss">
.files-container {
    display: flex;
    flex-wrap: wrap;
    padding: 10px;
    margin: 5px;

    .file-element {
        position: relative;
        max-width: 240;
        max-height: 320;
        padding: 10px;
    }

    .cross-delete {
        top: 0;
        right: 0;
    }

    .pagination {
        display: flex;
        list-style: none;
        padding: 0;
        margin: 0;
        justify-content: center;
    }

    .page-item {
        margin: 0 5px;
    }

    .page-link {
        display: block;
        padding: 10px 15px;
        text-align: center;
        background-color: #007BFF;
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .page-link:hover {
        background-color: #0056b3;
    }

    .active .page-link {
        background-color: #0056b3;
    }

    .page-link {
        display: block;
        padding: 10px 15px;
        text-align: center;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s, transform 0.3s;
    }

    .page-link:hover {
        background-color: #0056b3;
        transform: scale(1.05);
        /* Увеличиваем размер при наведении */
    }

    .active .page-link {
        background-color: #0056b3;
        transform: scale(1.05);
        /* Увеличиваем размер для активной страницы */
    }

    /* Анимация для перелистывания влево и вправо */
    .page-item {
        margin: 0 5px;
        transition: transform 0.3s;
    }

    .page-item.active-prev {
        transform: translateX(-10px);
        /* Для активной страницы слева */
    }

    .page-item.active-next {
        transform: translateX(10px);
        /* Для активной страницы справа */
    }

}
</style>
