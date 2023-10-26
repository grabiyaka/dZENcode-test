<template>
<div class="fileContent-container">
    <pre>{{ fileContent }}</pre>
    <div class="download-button">
        <button class="btn-save" v-if="!file" @click="downloadFile">
            <i class="bi bi-file-earmark-arrow-down"></i>
        </button>
        <button class="btn-close" v-if="!file" @click="">
            <i>&#10005</i>
        </button>
    </div>
</div>
</template>

<script>
export default {
    props: {
        filePath: String,
        file: null
    },
    data() {
        return {
            fileContent: '',
        };
    },
    mounted() {
        if(this.file){
            this.readFileContent();
        } else this.loadFileContent()
    },
    methods: {
        loadFileContent() {
            console.log(this.filePath);
            axios.get(this.filePath).then((response) => (this.fileContent = response.data, console.log(response.data)));
            
        },
        readFileContent() {
            const reader = new FileReader();
            reader.onload = (event) => {
                this.fileContent = event.target.result;
            };
            reader.readAsText(this.file); // Прочитываем содержимое файла
        },
        downloadFile() {

            const blob = new Blob([this.fileContent], {
                type: 'text/plain'
            });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'textfile.txt';
            a.click();
            window.URL.revokeObjectURL(url);
        },
    },
};
</script>

<style lang="scss">
.fileContent-container {
    position: relative;
    background: whitesmoke;
    padding: 5px;
    max-width: 100%;
    pre{
        max-height: 900px;
    }

    .download-button {
        position: absolute;
        top: 10px;
        left: 10px;
        transition: all ease .3s;

        // opacity: 0;
        // visibility: hidden;

        button {
            width: 50px;
            height: 50px;
            border: none;
            border-radius: 50%;
            
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 24px;
            cursor: pointer;

            i {
                font-size: 24px;
            }

            &.btn-save{
                background-color: #007bff;
            }

            &.btn-close{
                background-color: #d9534f;
            }
        }

        
    }

    // &:hover{
    //     .download-button {
    //         opacity: 1;
    //         visibility: visible;
    //     }
    // }
}
</style>
