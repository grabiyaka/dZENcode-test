<style lang="scss">
.files-container {
    display: flex;
    flex-wrap: wrap;
    padding: 10px;
    margin: 5px;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: 10px;

    .file-element {
        position: relative;
        width: 100px;
        height: 100px;
        overflow: hidden;
        border: 2px solid #ddd;
        border-radius: 5px;
        background-size: cover;
        background-position: center center;

        div {
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        i {
            font-size: 90px;
            width: 100%;
            height: 100%;
        }

        p {
            background: red;
        }
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
    }

    .page-item {
        margin: 0 5px;
        transition: transform 0.3s;
    }

    .page-item.active-prev {
        transform: translateX(-10px);
    }

    .page-item.active-next {
        transform: translateX(10px);
    }

}

.posts-header {
    justify-content: center;
    text-align: center;
}

.block-search {
    display: flex;
    align-items: center;
}

.block-search input {
    flex: 1;
    padding: 5px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 18px;
    margin: 8px 0;
    display: inline-block;
    box-sizing: border-box;
}

.block-search button {
    padding: 12px 20px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 18px;
    display: inline-block;
    box-sizing: border-box;
}

.dropdown {
    position: relative;
    display: inline-block;
}

/* Dropdown button */
.dropbtn {
    background-color: #3498db;
    color: white;
    padding: 10px;
    border: none;
    cursor: pointer;
}

/* Dropdown content (hidden by default) */
.dropdown-content {
    display: none;
    position: absolute;
    background-color: #f9f9f9;
    min-width: 160px;
    box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
    z-index: 1;
}

/* Dropdown links */
.dropdown-content a {
    color: black;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
    cursor: pointer;
}

/* Change color on hover */
.dropdown-content a:hover {
    background-color: #e9e9e9;
}

/* Show the dropdown menu on hover */
.dropdown:hover {
    .dropdown-content {
        display: block;
    }
}

/* Change color of the button on hover */
.dropdown:hover {
    .dropbtn {
        background-color: #2980b9;
    }
}
</style>
<template>
<div>
    <h1 id="title">Home</h1>
    <div v-if="$store.getters.getUser?.id">
        Pleace write your comment
        <quill-editor ref="quillComponent"></quill-editor>
    </div>

    <div v-if="$store.getters.getPosts.length" id="posts">
        <div class="posts-header">
            <h2>Posts:</h2>

            <div class="dropdown">
                <button class="dropbtn">Sort By</button>
                <div class="dropdown-content">
                    <a @click="getPosts('user_name', 'asc')">Name</a>
                    <a @click="getPosts('created_at','asc')">Date (Newest)</a>
                    <a @click="getPosts('created_at', 'desc')">Date (Oldest)</a>
                </div>
            </div>
        </div>
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
        async getPosts(sortOrder, sortDirection) {
            try {
                const response = await axios.get(`/api/posts?page=${this.paginate.currentPage}&sortField=${sortOrder}&sortDirection=${sortDirection}`);
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
