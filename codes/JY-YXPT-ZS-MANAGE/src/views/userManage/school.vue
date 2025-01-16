<template>
  <div class="app-container">
    <!-- 查询操作 -->
    <div class="filter-container">
      <el-row type="flex" justify="space-between">
        <el-col :span="20">
          <!-- 查询 -->
          <el-form ref="form" inline>
            <el-select v-model="query.search_field" style="width: 150px">
              <el-option value="username" label="用户名称" />
              <el-option value="school_name" label="绑定幼儿园" />
            </el-select>
            <el-form-item>
              <el-input
                v-model="query.search_value"
                placeholder="请输入关键词"
                clearable
                prefix-icon="el-icon-search"
              />
            </el-form-item>
            <el-form-item>
              <el-button type="primary" @click="getList"> 确定 </el-button>
            </el-form-item>
          </el-form>
        </el-col>
        <el-col :span="4" class="clearfix">
          <el-button
            type="primary"
            icon="el-icon-circle-plus-outline"
            class="fr"
            @click="add"
          >
            添加管理员
          </el-button>
        </el-col>
      </el-row>
    </div>
    <!-- table -->
    <table-box>
      <el-table
        :data="tableList"
        v-loading="loading"
        border
        style="width: 100%"
        height="100%"
      >
        <el-table-column
          prop="username"
          label="账号"
          min-width="150"
        ></el-table-column>
        <el-table-column
          prop="school_name"
          label="绑定幼儿园"
          min-width="150"
        ></el-table-column>
        <el-table-column
          prop="login_time"
          label="最近访问时间"
          min-width="150"
        ></el-table-column>
        <el-table-column prop="is_disable" label="状态" width="120">
          <template slot-scope="{ row }">
            <el-tag v-if="row.is_disable == 0" type="success" effect="dark"
              >激活</el-tag
            >
            <el-tag v-else type="danger" effect="dark">禁用</el-tag>
          </template>
        </el-table-column>
        <el-table-column label="操作" width="250">
          <template slot-scope="{ row }">
            <el-button type="primary" size="mini" @click="edit(row)"
              >编辑</el-button
            >
            <el-button
              type="primary"
              size="mini"
              v-if="row.is_disable == 0"
              @click="disableAuthPersonnel(row.id, 1)"
              >禁用</el-button
            >
            <el-button
              type="primary"
              size="mini"
              v-else
              @click="disableAuthPersonnel(row.id, 0)"
              >开启</el-button
            >
            <el-button type="primary" size="mini" @click="delAuthPersonnel(row)"
              >删除</el-button
            >
          </template>
        </el-table-column>
      </el-table>
    </table-box>
    <!-- pagination -->
    <div class="pagination">
      <pagination
        v-show="count > 0"
        :total="count"
        :page.sync="query.page"
        :limit.sync="query.limit"
        @pagination="getList"
      />
    </div>
    <!-- 添加修改 -->
    <el-dialog
      :title="dialogTitle"
      :visible.sync="dialog"
      :before-close="cancel"
      :close-on-click-modal="false"
      :close-on-press-escape="false"
      width="600px"
    >
      <el-form
        ref="dialogForm"
        :rules="rules"
        :model="dialogForm"
        class="dialog-body"
        label-width="100px"
      >
        <el-form-item prop="admin_user_id" label="绑定用户">
          <el-select
            v-model="dialogForm.admin_user_id"
            placeholder="请选择用户"
            clearable
            filterable
          >
            <el-option
              v-for="item in userList"
              :key="item.admin_user_id"
              :label="item.nickname"
              :value="item.admin_user_id"
            >
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item prop="school_id" label="绑定幼儿园">
          <el-select
            v-model="dialogForm.school_id"
            placeholder="请选择幼儿园"
            clearable
            filterable
          >
            <el-option
              v-for="item in schoolList"
              :label="item.name"
              :value="item.id"
              :key="item.id"
            >
            </el-option>
          </el-select>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button :loading="loading" type="primary" @click="submit"
          >提交</el-button
        >
      </div>
    </el-dialog>
  </div>
</template>

<script>
import {
  getAuthList,
  addAuthPersonnel,
  editAuthPersonnel,
  getAuthPersonnelInfo,
  delAuthPersonnel,
  disableAuthPersonnel,
} from "@/api/user/school";
import { list } from "@/api/admin/user";
import { getSchoolList } from "@/api/baseInfo/school";
export default {
  name: "UserSchoolManage",
  data() {
    return {
      query: {
        //查询参数
        page: 1, //页数
        limit: 15, //每页条数
        search_field: "username", //搜索类型
        search_value: "", //搜索关键词
        source_type: 0, //类型，0学校管理员 1区域管理员
      },
      tableList: [], //数据列表
      count: 1, //列表总条数
      loading: false,
      dialog: false, //是否展示弹窗
      dialogTitle: "", //弹窗标题
      //弹窗表单
      dialogForm: {},
      // 用户列表
      userList: [],
      // 幼儿园列表
      schoolList: [],
      rules: {
        //弹窗表单验证
        admin_user_id: [
          { required: true, message: "请选择用户", trigger: "blur" },
        ],
        school_id: [
          { required: true, message: "请选择幼儿园", trigger: "blur" },
        ],
      },
    };
  },
  created() {
    this.initForm();
    this.getList();
    this.getSchoolList();
  },
  methods: {
    // 禁用、开启
    async disableAuthPersonnel(id, is_disable) {
      let params = {
        ids: id,
        is_disable,
      };
      let res = await disableAuthPersonnel(params);
      this.$message.success(res.msg);
      this.getList();
    },
    // 删除
    async delAuthPersonnel(row) {
      this.$confirm("确定要删除吗？", "提示", {
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        type: "warning",
      })
        .then(async () => {
          let params = {
            ids: row.id,
          };
          let res = await delAuthPersonnel(params);
          this.$message({
            message: "删除成功！",
            type: "success",
          });
          this.getList();
        })
        .catch(() => {});
    },
    // 获取权限人员信息
    async getAuthPersonnelInfo(id) {
      let params = {
        id,
      };
      let res = await getAuthPersonnelInfo(params);
      //弹窗表单
      this.dialogForm = {
        id,
        admin_user_id: Number(res.data.admin_user_id), //绑定用户
        school_id: Number(res.data.school_id), //绑定幼儿园
        source_type: 0, //类型，0学校管理员 1区域管理员
      };
    },
    // 获取学校列表
    async getSchoolList() {
      let params = {
        page: 1,
        limit: 1000,
      };
      let res = await getSchoolList(params);
      this.schoolList = res.data.list;
    },
    // 获取用户列表
    async getUserList() {
      let params = {
        page: 1,
        limit: 1000,
        search_field: 'is_super',
        search_value: 0,
      };
      let res = await list(params);
      this.userList = res.data.list;
    },
    // 提交
    submit() {
      this.$refs["dialogForm"].validate(async (valid) => {
        if (valid) {
          this.loading = true;
          try {
            let res = await (this.dialogForm.id
              ? editAuthPersonnel(this.dialogForm)
              : addAuthPersonnel(this.dialogForm));
            this.loading = false;
            this.cancel();
            this.$message.success(res.msg);
            this.getList();
          } catch (error) {
            this.loading = false;
          }
        }
      });
    },
    // 获取列表
    async getList() {
      this.loading = true;
      let res = await getAuthList(this.query);
      this.tableList = res.data.list;
      this.count = res.data.count;
      this.loading = false;
    },
    // 关闭弹窗
    cancel() {
      this.dialog = false;
      this.initForm();
      this.$refs["dialogForm"].resetFields();
    },
    // 重置弹窗表单
    initForm() {
      this.dialogForm = {
        //弹窗表单
        id: "",
        admin_user_id: "", //绑定用户
        school_id: "", //绑定幼儿园
        source_type: 0, //类型，0学校管理员 1区域管理员
      };
    },
    // 添加
    add() {
      this.getUserList();
      this.dialog = true;
      this.dialogTitle = "添加管理员";
    },
    // 编辑
    edit(row) {
      this.getUserList();
      this.dialog = true;
      this.dialogTitle = "编辑管理员";
      this.getAuthPersonnelInfo(row.id);
    },
  },
};
</script>

<style lang="scss" scoped>
</style>