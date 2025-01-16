<template>
	<view class="study container">
		<base-search @search="search" :isFixed="true"></base-search>
		<block v-if="isLogin">
			<view class="list">
				<view class="item" v-for="(item, index) in list" :key="index" @tap="goto(item.id, item.curriculum_id)">
					<view class="name row-between">
						<text class="line1 mr10 bold f36">{{ item.curriculum_name }}</text>
						<view class="tag f26 row up" v-if="item.type == 0">线上</view>
						<view class="tag f26 row down" v-if="item.type == 1">线下</view>
					</view>
					<view class="school muted f24 mt5">{{ item.school_name }}</view>
					<view class="pic mt30"><u-image :src="item.cover_pic_url" width="100%" height="100%" border-radius="20"></u-image></view>
				</view>
			</view>
			<loading-footer :status="status"></loading-footer>
		</block>
		<my-empty :slotEmpty="true" v-else><u-button type="primary" hover-class="none" @click="toLogin">去登录</u-button></my-empty>
	</view>
</template>

<script>
import { toLogin } from '@/utils/login.js';
import { getMyCourse } from '@/api/user.js';
import { loadingFun } from '@/utils/tools.js';
import { loadingType } from '@/utils/types.js';
export default {
	data() {
		return {
			keyword: '',
			list: [],
			page: 1,
			status: loadingType.LOADING
		};
	},
	onLoad() {
		this.getMyCourse();
	},
	methods: {
		search(e) {
			this.page = 1;
			this.status = loadingType.LOADING;
			this.list = [];
			this.keyword = e;
			this.getMyCourse();
		},
		// 获取我的课程
		async getMyCourse() {
			if (!this.isLogin) return;
			let { page, list, status } = this;
			let params = {
				keyword: this.keyword
			};
			let res = await loadingFun(getMyCourse, page, list, status, params);
			this.page = res.page;
			this.status = res.status;
			this.list = res.dataList;
		},
		toLogin,
		goto(order_id, course_id) {
			uni.navigateTo({
				url: `/pages/study-details/study-details?order_id=${order_id}&course_id=${course_id}`
			});
		}
	},
	onReachBottom() {
		if (this.status != loadingType.FINISHED) {
			this.getMyCourse();
		}
	}
};
</script>

<style lang="scss" scoped>
.study {
	.list {
		.item {
			padding: 26rpx 28rpx 36rpx;
			background-color: #ffffff;
			border-radius: 20rpx;
			margin-bottom: 24rpx;
			&:last-of-type {
				margin-bottom: 0;
			}
			.name {
				text {
					line-height: 50rpx;
				}
				.tag {
					padding: 4rpx 24rpx;
					border-radius: 100rpx;
					&.down {
						background: rgba(255, 153, 32, 0.06);
						color: #ff9920;
						border: 2rpx solid #ff9920;
					}
					&.up {
						background: rgba(32, 135, 255, 0.06);
						color: #2087ff;
						border: 2rpx solid #2087ff;
					}
				}
			}
			.school {
				line-height: 34rpx;
			}
			.pic {
				height: 320rpx;
				overflow: hidden;
			}
		}
	}
	/deep/.my-empty {
		height: calc(100vh - 78rpx);
	}
}
</style>
