

import csv

import torch
from sklearn.metrics.pairwise import cosine_similarity
import torch.nn.functional as F
import numpy as np
from transformers import AutoTokenizer, AutoModel

# 使用 `transformers` 模块中的 `AutoTokenizer`、`AutoModel` 分别加载 `tokenizer` 和 `model`。
def load_model(model_name):
    # 加载模型
    model = AutoModel.from_pretrained(model_name)
    model.eval()

    # 加载分词器
    tokenizer = AutoTokenizer.from_pretrained(model_name)
    return model, tokenizer

def verify_similarity(original, modified, model, tokenizer):
    # 确保模型处于评估模式
    
    model.eval()

    #使用模型推理original

    # 对原始文本和修改后的文本进行编码
    original_encoding = tokenizer(original, return_tensors='pt', padding=True, truncation=True, max_length=512)
    modified_encoding = tokenizer(modified, return_tensors='pt', padding=True, truncation=True, max_length=512)

    with torch.no_grad():
        # 获取原始文本的隐藏状态
        original_outputs = model(**original_encoding)
        pt_predection = F.softmax(original_outputs[0], dim=-1)
        print(f"pt_predection: {pt_predection}")
        
        
        original_hidden_state = original_outputs.last_hidden_state.mean(dim=1)

        # 获取修改后文本的隐藏状态
        modified_outputs = model(**modified_encoding)
        modified_hidden_state = modified_outputs.last_hidden_state.mean(dim=1)

        # 计算余弦相似度
        similarity = F.cosine_similarity(original_hidden_state, modified_hidden_state)
        return similarity.item()

#循环按行分别读取original_text.csv和modified_text.csv文件的第二列,按行读取评估文本相似度
original_text = []
modified_text = []
with open('original_text.csv', 'r') as f:
    reader = csv.reader(f)
    for row in reader:
        original_text.append(row[1])
with open('modified_text.csv', 'r') as f:
    reader = csv.reader(f)
    for row in reader:
        modified_text.append(row[1])

model_name = "./Sentiment_classification_model" 
model, tokenizer = load_model(model_name)

for i in range(len(original_text)):
    
    #如果分数小于75，则输出不相似的文本
    # print(f"Similarity score: {similarity_score}")
    if original_text[i] == modified_text[i]:
        print(f"Index: {i-1}")
        print(f"not modified_text: {original_text[i]}")
        continue
    similarity_score = verify_similarity(original_text[i], modified_text[i], model, tokenizer)
    if similarity_score < 0.75:
        print(f"Index: {i-1}")
        print(f"original_text: {original_text[i]}")
        print(f"modified_text: {modified_text[i]}")
        print(f"Warning!!!Similarity score: {similarity_score}")
        print("\n")
        

# # 示例调用
# model_name = "./Sentiment_classification_model" 
# model, tokenizer = load_model(model_name)



# similarity_score = verify_similarity(original_text, modified_text, model, tokenizer)
# print(f"Similarity score: {similarity_score}")