<?php

namespace Fincode\Laravel\Enums;

enum FinHistoryType: int
{
    // 新規登録
    case INSERT = 1;

    // 更新
    case UPDATE = 2;

    // 論理削除
    case DELETE = 3;

    // 復元
    case RESTORE = 4;

    // 物理削除
    case FORCE_DELETE = 5;
}
