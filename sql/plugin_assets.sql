-- ----------------------------
-- Table structure for plugin_assets_contract
-- ----------------------------
DROP TABLE IF EXISTS `plugin_assets_contract`;
CREATE TABLE `plugin_assets_contract`  (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '合同名称',
  `number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '合同编号',
  `company` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '中标公司',
  `signing_date` date NULL DEFAULT NULL COMMENT '签订日期',
  `due_date` date NULL DEFAULT NULL COMMENT '到期日期',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '内容',
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '合同附件路径非必填',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '描述备注',
  `notification_id` int(10) NULL DEFAULT NULL COMMENT '告警邮箱',
  `due_alarm` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '未告警' COMMENT '到期告警描述',
  `last_modified` datetime(0) NULL DEFAULT NULL COMMENT '最后修改时间',
  `modified_by` int(10) NULL DEFAULT NULL COMMENT '修改人',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for plugin_assets_documents
-- ----------------------------
DROP TABLE IF EXISTS `plugin_assets_documents`;
CREATE TABLE `plugin_assets_documents`  (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '名称',
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '路径',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '描述说明',
  `last_modified` datetime(0) NULL DEFAULT NULL COMMENT '最后修改时间',
  `modified_by` int(10) NULL DEFAULT NULL COMMENT '修改人',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for plugin_assets_equipment
-- ----------------------------
DROP TABLE IF EXISTS `plugin_assets_equipment`;
CREATE TABLE `plugin_assets_equipment`  (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '名称',
  `model` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '型号',
  `purpose` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '用途',
  `total` int(10) NULL DEFAULT 0 COMMENT '总数量',
  `last_modified` datetime(0) NULL DEFAULT NULL COMMENT '最后修改时间',
  `modified_by` int(10) NULL DEFAULT NULL COMMENT '修改人',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for plugin_assets_equipment_almacenar
-- ----------------------------
DROP TABLE IF EXISTS `plugin_assets_equipment_almacenar`;
CREATE TABLE `plugin_assets_equipment_almacenar`  (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `equipment_id` int(10) NOT NULL COMMENT '设备ID',
  `operation_type` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '操作类型：出库，入库',
  `contract_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '合同编号入库填写',
  `lend_person` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '借出人出库时填写',
  `count` int(11) NULL DEFAULT 0 COMMENT '数量',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '备注说明',
  `last_modified` datetime(0) NULL DEFAULT NULL COMMENT '最后修改时间',
  `modified_by` int(10) NULL DEFAULT NULL COMMENT '修改人',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `index_1`(`equipment_id`) USING BTREE COMMENT '设备主键ID'
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for plugin_assets_ipaddress
-- ----------------------------
DROP TABLE IF EXISTS `plugin_assets_ipaddress`;
CREATE TABLE `plugin_assets_ipaddress`  (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `ip` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT 'IP地址',
  `group_id` int(10) NULL DEFAULT NULL COMMENT 'IP地址组ID',
  `use_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '使用人',
  `use_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '使用地址',
  `use_uso` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '使用用途',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '描述备注',
  `last_modified` datetime(0) NULL DEFAULT NULL COMMENT '最后修改时间',
  `modified_by` int(10) NULL DEFAULT NULL COMMENT '修改人',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for plugin_assets_ipaddress_group
-- ----------------------------
DROP TABLE IF EXISTS `plugin_assets_ipaddress_group`;
CREATE TABLE `plugin_assets_ipaddress_group`  (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT 'IP地址组名称',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '描述备注',
  `last_modified` datetime(0) NULL DEFAULT NULL COMMENT '最后修改时间',
  `modified_by` int(10) NULL DEFAULT NULL COMMENT '修改人',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;
