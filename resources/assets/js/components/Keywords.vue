<template>
    <el-row>
        <el-row>
            <el-button @click="handleKeywordCreate()" type="text" icon="el-icon-plus">Create Keywords</el-button>
        </el-row>
        <el-dialog title="Create Keywords" :visible.sync="isCreate">
            <el-form :model="form">
                <el-form-item label="Keyword">
                    <el-input type="textarea" v-model="form.keywords" auto-complete="off" :rows=5></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="isCreate = false">Cancel</el-button>
                <el-button type="primary" @click="handleKeywordCreateConfirm()">Confirm</el-button>
            </div>
        </el-dialog>
        <el-dialog title="Edit Keyword" :visible.sync="isEdit">
            <el-form :model="form">
                <el-form-item label="Keyword">
                    <el-input v-model="form.keyword" disabled></el-input>
                </el-form-item>
                <el-form-item label="Keyword Category">
                    <el-input v-model="form.keyword_category" auto-complete="off"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="isEdit = false">Cancel</el-button>
                <el-button type="primary" @click="handleKeywordEditConfirm()">Confirm</el-button>
            </div>
        </el-dialog>
        <el-dialog title="Delete Keyword" :visible.sync="isDelete">
            <span>Are you sure you want to delete this keyword?</span>
            <span slot="footer" class="dialog-footer">
                <el-button @click="isDelete = false">Cancel</el-button>
                <el-button type="danger" @click="handleKeywordDeleteConfirm()">Confirm</el-button>
            </span>
        </el-dialog>
        <el-dialog title="Create Task" :visible.sync="isTaskCreate">
            <el-form :model="form">
                <el-form-item>
                    <span>Keyword <strong>{{form.keyword}}</strong></span>
                </el-form-item>
                <el-form-item label="Search Engines">
                    <el-select v-model="form.searchEngines" multiple collapse-tags placeholder="Select...">
                        <el-option
                            v-for="item in form.searchEngineOptions"
                            :key="item"
                            :label="item"
                            :value="item">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="Result Number">
                    <el-select v-model="form.keywordResultNumber" placeholder="Select...">
                        <el-option
                            v-for="item in form.keywordResultNumberOptions"
                            :key="item"
                            :label="item"
                            :value="item">
                        </el-option>
                    </el-select>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="isTaskCreate = false">Cancel</el-button>
                <el-button type="primary" @click="handleTaskCreateConfirm()">Confirm</el-button>
            </div>
        </el-dialog>
        <el-table :data="data">
            <el-table-column type="expand">
                <template slot-scope="props">
                    <el-form label-position="left" inline class="table-expand">
                        <el-form-item label="ID">
                            <span>{{ props.row.id }}</span>
                        </el-form-item>
                        <el-form-item label="Keyword">
                            <span>{{ props.row.keyword }}</span>
                        </el-form-item>
                        <el-form-item label="Keyword Category">
                            <span>{{ props.row.keyword_category }}</span>
                        </el-form-item>
                        <el-form-item label="Baidu Crawler Exec Date">
                            <span>{{ props.row.baidu_crawler_exec_date }}</span>
                        </el-form-item>
                        <el-form-item label="Bing Crawler Exec Date">
                            <span>{{ props.row.bing_crawler_exec_date }}</span>
                        </el-form-item>
                        <el-form-item label="360 Crawler Exec Date">
                            <span>{{ props.row['360_crawler_exec_date'] }}</span>
                        </el-form-item>
                    </el-form>
                </template>
            </el-table-column>
            <el-table-column label="ID" prop="id"></el-table-column>
            <el-table-column label="Keyword" prop="keyword"></el-table-column>
            <el-table-column label="Keyword Category" prop="keyword_category"></el-table-column>
            <el-table-column label="Operation">
                <template slot-scope="scope">
                    <el-button @click="handleTaskCreate(scope.row)" type="info" icon="el-icon-message" size="mini" circle></el-button>
                    <el-button @click="handleKeywordEdit(scope.row)" type="primary" icon="el-icon-edit" size="mini" circle></el-button>
                    <el-button @click="handleKeywordDelete(scope.row)" type="danger" icon="el-icon-delete" size="mini" circle></el-button>
                </template>
            </el-table-column>
        </el-table>
        <el-pagination
            class="table-pagination"
            background
            layout="prev, pager, next"
            :total="total"
            :page-size="per_page"
            @current-change="handlePageChange">
        </el-pagination>
    </el-row>
</template>
<style>
.table-expand {
    font-size: 0;
}
.table-expand label {
    color: #99a9bf;
}
.table-expand .el-form-item {
    margin-right: 0;
    margin-bottom: 0;
    width: 50%;
}
.table-pagination {
    text-align: center;
    margin: 10px 0;
}
</style>
<script>
import http from 'axios'
export default {
    data() {
        return {
            current_page: 1,
            per_page: 1,
            data: [],
            total: 0,
            form: {},
            isCreate: false,
            isEdit: false,
            isDelete: false,
            isTaskCreate: false,
            checkList: []
        }
    },
    created: function() {
        this.getKeywords(this.current_page)
    },
    methods: {
        handleKeywordCreate: function() {
            this.isCreate = true
            this.form = {}
        },
        handleKeywordEdit: function(row) {
            this.isEdit = true
            this.form = row
        },
        handleKeywordDelete: function(row) {
            this.isDelete = true
            this.form = row
        },
        handleTaskCreate: function(row) {
            this.isTaskCreate = true
            this.form = {
                ...row,
                searchEngines: ['baidu', 'bing', '360'],
                searchEngineOptions: ['baidu', 'bing', '360'],
                keywordResultNumber: 10,
                keywordResultNumberOptions: [10,20,50]
            }
        },
        handleKeywordCreateConfirm: async function() {
            let keywords = this.form.keywords.split('\n').map(e => e.trim())
            for(let keyword of keywords) {
                try {
                    await this.createKeyword(keyword)
                } catch(err) {
                    console.log(err)
                }
            }
            await this.getKeywords(this.current_page)
            this.isCreate = false
            this.$message({
                message: 'Create keywords success!',
                type: 'success'
            })
        },
        handleKeywordEditConfirm: async function() {
            await this.updateKeyword(this.form)
            this.isEdit = false
            this.$message({
                message: 'Update keyword success!',
                type: 'success'
            })
        },
        handleKeywordDeleteConfirm: async function() {
            await this.deleteKeyword(this.form)
            await this.getKeywords(this.current_page)
            this.isDelete = false
            this.$message({
                message: 'Delete keyword success!',
                type: 'success'
            })
        },
        handleTaskCreateConfirm: async function() {
            await this.createTask(this.form)
            this.isTaskCreate = false
            this.$message({
                message: 'Create task success!',
                type: 'success'
            })
        },
        handlePageChange: async function(page) {
            await this.getKeywords(page)
        },
        createKeyword: function(data) {
            return http.post(`/api/keywords`, {
                keyword: data
            })
        },
        getKeywords: function(page) {
            return http.get(`/api/keywords?page=${page}`).then(resp => {
                let { data, current_page, per_page, total } = resp.data
                this.data = data
                this.current_page = current_page
                this.per_page = per_page
                this.total = total
            })
        },
        updateKeyword: function(data) {
            return http.put(`/api/keywords/${data.id}`, {
                keyword_category: data.keyword_category
            })
        },
        deleteKeyword: function(data) {
            return http.delete(`/api/keywords/${data.id}`)
        },
        createTask: function(data) {
            return http.post('/api/tasks/', {
                keyword_id: data.id,
                keyword_result_number: data.keywordResultNumber,
                search_engines: data.searchEngines,
                status: 'Pending'
            })
        }
    }
}
</script>

