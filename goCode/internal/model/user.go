package model

import (
	"time"

	"gorm.io/gorm"
)

type User struct {
	ID        uint           `gorm:"primarykey" json:"id"`
	CreatedAt time.Time      `json:"created_at"`
	UpdatedAt time.Time      `json:"updated_at"`
	DeletedAt gorm.DeletedAt `gorm:"index" json:"-"`
	Username  string         `gorm:"size:50;unique;not null" json:"username"`
	Password  string         `gorm:"size:255;not null" json:"-"`
	Nickname  string         `gorm:"size:50" json:"nickname"`
	Email     string         `gorm:"size:100" json:"email"`
	Phone     string         `gorm:"size:20" json:"phone"`
	Status    int            `gorm:"default:1" json:"status"` // 1:正常 0:禁用
	Role      string         `gorm:"size:20;default:user" json:"role"` // admin, user
	Avatar    string         `gorm:"size:255" json:"avatar"`
}

func (User) TableName() string {
	return "user"
}
