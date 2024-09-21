# WebShell 绕过挑战 README

## 介绍

欢迎来到 AI WebShell 绕过挑战！在本挑战中，你将面对一个由 AI 驱动的 WebShell 检测器。你的目标是构造一个可以绕过当前检测机制的 WebShell。检测器会根据你提交的 WebShell 文件进行检测并返回其判断结果。如果你成功绕过检测，将获得最终的 Flag 作为奖励。

## 挑战目标

你的任务是编写一个 WebShell，成功绕过检测器的防护，获取 Flag。

## 挑战流程

1. **构造 WebShell**: 你需要设计并编写一个 WebShell 文件，尽可能绕过我们的检测机制。

2. **提交检测**: 将你的 WebShell 文件提交给检测器进行检测。检测器会分析你的文件，并返回以下信息：
    - **类别判断**: 检测器会给出 WebShell 的预测类别。
    - **预测概率**: 检测器会返回各类别对应的预测概率。

3. **获取反馈**: 根据检测器的反馈，你可以选择调整你的 WebShell 文件，优化绕过策略。

4. **成功绕过**: 如果你的 WebShell 成功绕过检测器的防护，你将获得 Flag。否则，你可以根据检测器的反馈继续修改并重新提交。

## 检测反馈示例

当你提交一个 WebShell 文件后，你将收到类似如下格式的反馈：

```
result_class: Webshell, result_proba: 0.6904913187026978

意为：当前上传的文件检测为webshell，并且为webshell的概率为69%

result_class: whitefile, result_proba: 0.6904913187026978

意为：当前上传的文件检测为whitefile，并且为whitefile的概率为69%。即绕过成功。
```

根据这些信息，你可以了解你的 WebShell 在检测器眼中的可信度，从而进一步调整策略。

## 规则

1. **文件格式**: 你提交的 WebShell 文件可以是PHP文件格式，符合标准 WebShell 的基本特性。

2. **提交限制**: 每位选手可以多次提交文件。

3. **文件上传**: 只有在检测器无法检测到你的 WebShell 的情况下，文件可上传成功。

4. **Flag 获取**: webshell上传成功，访问webshell界面，执行系统命令，得到flag

![image](https://c.img.dasctf.com/LightPicture/2024/08/5d6cef9e32f4dc9e.png)

