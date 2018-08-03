<template>
    <div>
        <el-dialog title="Delete Task" :visible.sync="isDelete">
            <span>Are you sure you want to delete this Task?</span>
            <span slot="footer" class="dialog-footer">
                <el-button @click="isDelete = false">Cancel</el-button>
                <el-button type="danger" @click="handleTaskDeleteConfirm()">Confirm</el-button>
            </span>
        </el-dialog>
        <el-table :data="data">
            <el-table-column label="ID" prop="id"></el-table-column>
            <el-table-column label="Keyword" prop="keyword.keyword"></el-table-column>
            <el-table-column label="Keyword Category" prop="keyword.keyword_category"></el-table-column>
            <el-table-column label="Search Engines">
                <template slot-scope="scope">
                    <span>{{scope.row.search_engines.join(' | ')}}</span>
                </template>
            </el-table-column>
            <el-table-column label="Status" prop="status"></el-table-column>
            <el-table-column label="Operation">
                <template slot-scope="scope">
                    <el-button @click="handleTaskDelete(scope.row)" type="danger" icon="el-icon-delete" size="mini" circle></el-button>
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
            isDelete: false,
            form: {}
        }
    },
    created: function () {
        this.getTasks(this.current_page)
    },
    methods: {
        handleTaskDelete: async function(row) {
            this.isDelete = true
            this.form = row
        },
        handleTaskDeleteConfirm: async function() {
            await this.deleteTask(this.form)
            await this.getTasks(this.current_page)
            this.isDelete = false
            this.$message({
                message: 'Delete task success!',
                type: 'success'
            })
        },
        handlePageChange: async function(page) {
            await this.getTasks(page)
        },
        getTasks: function (page) {
            return http.get(`/api/tasks?page=${page}`).then((resp) => {
                    let { data, current_page, per_page, total } = resp.data
                    this.data = data
                    this.current_page = current_page
                    this.per_page = per_page
                    this.total = total
            })
        },
        deleteTask: function(data) {
            return http.delete(`/api/tasks/${data.id}`)
        },
    }
}
</script>

