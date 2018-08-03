<template>
    <div>
        <el-row>
            <el-button @click="handleSettingConfig" type="text" icon="el-icon-setting">Config Search Settings</el-button>
            <el-button @click="handleResultsDownload" type="text" icon="el-icon-download">Download</el-button>
            <span>
                <el-tag :key="key" v-for="(value, key) in settings" style="margin-right: 5px">{{`${key}: ${value}`}}</el-tag>
            </span>
        </el-row>
        <el-dialog title="Settings" :visible.sync="isSetting">
            <el-form :model="form" :inline="true" class="inline-form">
                <el-form-item label="Keyword">
                    <el-input v-model="form.keyword" auto-complete="off"></el-input>
                </el-form-item>
                <el-form-item label="Keyword Category">
                    <el-input v-model="form.keyword_category" auto-complete="off"></el-input>
                </el-form-item>
                <el-form-item label="Search Engine">
                    <el-input v-model="form.search_engine" auto-complete="off"></el-input>
                </el-form-item>
                <el-form-item label="IP">
                    <el-input v-model="form.ip" auto-complete="off"></el-input>
                </el-form-item>
                <el-form-item label="IP Region">
                    <el-input v-model="form.ip_region" auto-complete="off"></el-input>
                </el-form-item>
                <el-form-item label="Title">
                    <el-input v-model="form.title" auto-complete="off"></el-input>
                </el-form-item>
                <el-form-item label="Description">
                    <el-input v-model="form.description" auto-complete="off"></el-input>
                </el-form-item>
                <el-form-item label="URL">
                    <el-input v-model="form.url" auto-complete="off"></el-input>
                </el-form-item>
                <el-form-item label="URL Redirected">
                    <el-input v-model="form.url_redirected" auto-complete="off"></el-input>
                </el-form-item>
                <el-form-item label="URL Category">
                    <el-input v-model="form.url_category" auto-complete="off"></el-input>
                </el-form-item>
                <el-form-item label="URL Status">
                    <el-input v-model="form.url_status" auto-complete="off"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="isSetting = false">Cancel</el-button>
                <el-button type="primary" @click="handleSettingUpdateConfirm()">Confirm</el-button>
            </div>
        </el-dialog>
        <el-dialog title="Edit Result" :visible.sync="isEdit">
            <el-form :model="form">
                <el-form-item label="URL Category">
                    <el-input v-model="form.url_category" auto-complete="off"></el-input>
                </el-form-item>
                <el-form-item label="URL Status">
                    <el-input v-model="form.url_status" auto-complete="off"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="isEdit = false">Cancel</el-button>
                <el-button type="primary" @click="handleResultEditConfirm()">Confirm</el-button>
            </div>
        </el-dialog>
        <el-dialog title="Delete Result" :visible.sync="isDelete">
            <span>Are you sure you want to delete this result?</span>
            <span slot="footer" class="dialog-footer">
                <el-button @click="isDelete = false">Cancel</el-button>
                <el-button type="danger" @click="handleResultDeleteConfirm()">Confirm</el-button>
            </span>
        </el-dialog>
        <el-dialog title="Download Results" :visible.sync="isDownload">
            <el-form :model="form">
                <el-form-item label="Latest N Results">
                    <el-input v-model="form.number" auto-complete="off"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="isDownload = false">Cancel</el-button>
                <el-button type="primary" @click="handleResultsDownloadConfirm()">Confirm</el-button>
            </div>
        </el-dialog>
        <el-table :data="data">
            <el-table-column type="expand">
                <template slot-scope="props">
                    <el-form label-position="left" inline class="table-expand">
                        <el-form-item label="Title">
                            <span>{{ props.row.title }}</span>
                        </el-form-item>
                        <el-form-item label="Description">
                            <span>{{ (props.row.description || '').substring(0, 50) + '...' }}</span>
                        </el-form-item>
                        <el-form-item label="URL">
                            <a v-bind:href="props.row.url">Click Me</a>
                        </el-form-item>
                        <el-form-item label="URL Redirected">
                            <a v-bind:href="props.row.url_redirected">Click Me</a>
                        </el-form-item>
                        <el-form-item label="URL Category">
                            <span>{{ props.row.url_category }}</span>
                        </el-form-item>
                        <el-form-item label="URL Status">
                            <span>{{ props.row.url_status }}</span>
                        </el-form-item>
                    </el-form>
                </template>
            </el-table-column>
            <el-table-column label="ID" prop="id"></el-table-column>
            <el-table-column label="Keyword" prop="keyword.keyword"></el-table-column>
            <el-table-column label="Keyword Category" prop="keyword.keyword_category"></el-table-column>
            <el-table-column label="Search Engine" prop="search_engine"></el-table-column>
            <el-table-column label="IP" prop="ip"></el-table-column>
            <el-table-column label="IP Region" prop="ip_region"></el-table-column>
            <el-table-column label="Operation">
                <template slot-scope="scope">
                    <el-button @click="handleResultEdit(scope.row)" type="primary" icon="el-icon-edit" size="mini" circle></el-button>
                    <el-button @click="handleResultDelete(scope.row)" type="danger" icon="el-icon-delete" size="mini" circle></el-button>
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
    </div>
</template>
<style>
.inline-form label {
    width: 140px;
}
</style>
<script>
import http from 'axios'
import XLSX from 'xlsx'
export default {
    data() {
        return {
            current_page: 1,
            per_page: 1,
            data: [],
            total: 0,
            isEdit: false,
            isDelete: false,
            isSetting: false,
            isDownload: false,
            form: {},
            settings: {}
        }
    },
    created: function () {
        this.getResults(this.current_page)
    },
    methods: {
        handleResultsDownload: function () {
            this.isDownload = true
            this.form = {}
        },
        handleResultsDownloadConfirm: async function () {
            await this.getResults(1, this.form.number, function (resp) {
                let { data } = resp.data
                
                let worksheet = XLSX.utils.json_to_sheet(data.map(item => {
                    return Object.assign({}, ...function flatten(e, path = '') {
                        return e ? [].concat(
                            ...Object.keys(e).map(
                                key => typeof e[ key ] === 'object'
                                ? flatten( e[ key ], `${ path }/${ key }` )
                                : ( { [ `${ path }/${ key }` ]: e[ key ] } )
                            )
                        ) : []
                    }(item))
                }))
                let workbook = XLSX.utils.book_new()
                XLSX.utils.book_append_sheet(workbook, worksheet, "Sheet 1")
                XLSX.writeFile(workbook, 'results.xlsb')
            })
            this.isDownload = false
            this.$message({
                message: 'Download results success!',
                type: 'success'
            })
        },
        handleSettingConfig: function () {
            this.isSetting = true
            this.form = Object.assign({}, this.settings)
        },
        handleSettingUpdateConfirm: async function () {
            this.isSetting = false
            Object.keys(this.form).forEach(key => (!Boolean(this.form[key]) && delete this.form[key]));
            this.settings = Object.assign({}, this.form)
            this.current_page = 1
            await this.getResults(this.current_page);
            this.$message({
                message: 'Config search settings success!',
                type: 'success'
            })
        },
        handleResultEditConfirm: async function () {
            await this.updateResult(this.form)
            this.isEdit = false
            this.$message({
                message: 'Update result success!',
                type: 'success'
            })
        },
        handleResultDeleteConfirm: async function () {
            await this.deleteResult(this.form)
            await this.getResults(this.current_page)
            this.isDelete = false
            this.$message({
                message: 'Delete result success!',
                type: 'success'
            })
        },
        handleResultDelete: function (row) {
            this.isDelete = true
            this.form = row
        },
        handleResultEdit: function (row) {
            this.isEdit = true
            this.form = row
        },
        handlePageChange: async function(page) {
            await this.getResults(page)
        },
        getResults: function (page, per_page=10, callback) {
            let keys = Object.keys(this.settings);
            return http.get(keys.length 
            ? `/api/results/s?${keys.map((key) => `${key}=${this.settings[key]}`).join('&')}&page=${page}&per_page=${per_page}`
            : `/api/results?page=${page}&per_page=${per_page}`)
            .then((resp) => {
                if(callback) {
                    callback(resp)
                } else {
                    let { data, current_page, per_page, total } = resp.data
                    this.data = data
                    this.current_page = current_page
                    this.per_page = parseInt(per_page)
                    this.total = total
                }
            })

        },
        updateResult: function(data) {
            return http.put(`/api/results/${data.id}`, {
                url_category: data.url_category,
                url_status: data.url_status
            })
        },
        deleteResult: function(data) {
            return http.delete(`/api/results/${data.id}`)
        },
    }
}
</script>

