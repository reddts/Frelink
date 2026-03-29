#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""
Publish a complete content chain:
1. Discover current hot FAQ.
2. Create or reuse topics.
3. Publish one FAQ question.
4. Publish one self-answer.
5. Publish one article with a cover image.
6. Verify the published content.

Usage:
  API_TOKEN=... python3 scripts/publish_chain.py --theme "大数据展示平台"
"""

from __future__ import annotations

import argparse
import json
import os
import hashlib
import sys
import time
import urllib.error
import urllib.parse
import urllib.request
from typing import Any, Dict, List


def make_slug(value: str) -> str:
    digest = hashlib.sha1(value.strip().encode("utf-8")).hexdigest()[:10]
    return f"publish-chain-{digest}"


def request_payload(base_url: str, token: str, path: str, data: Dict[str, Any] | None = None) -> Dict[str, Any]:
    url = base_url.rstrip("/") + path
    body = None if data is None else urllib.parse.urlencode(data, doseq=True).encode("utf-8")
    headers = {"ApiToken": token}
    if body is not None:
        headers["Content-Type"] = "application/x-www-form-urlencoded"
    req = urllib.request.Request(url, data=body, headers=headers)
    try:
        with urllib.request.urlopen(req, timeout=30) as resp:
            raw = resp.read().decode("utf-8")
    except urllib.error.HTTPError as exc:
        raw = exc.read().decode("utf-8", errors="replace")
        raise RuntimeError(f"{path} failed with HTTP {exc.code}: {raw}") from exc
    except urllib.error.URLError as exc:
        raise RuntimeError(f"{path} failed: {exc}") from exc

    try:
        payload = json.loads(raw)
    except json.JSONDecodeError as exc:
        raise RuntimeError(f"{path} returned invalid JSON: {raw}") from exc

    return payload


def request_json(base_url: str, token: str, path: str, data: Dict[str, Any] | None = None) -> Dict[str, Any]:
    payload = request_payload(base_url, token, path, data)
    if payload.get("code") != 1:
        raise RuntimeError(f"{path} failed: {payload}")
    return payload


def ensure_topic(base_url: str, token: str, title: str) -> Dict[str, Any]:
    payload = request_payload(base_url, token, "/api/Topic/create", {"title": title})
    if payload.get("code") == 1:
        if (payload.get("data") or {}).get("status") == "pending_review":
            return {
                "id": 0,
                "title": title,
                "status": "pending_review",
                "approval_id": int((payload.get("data") or {}).get("approval_id", 0)),
            }
        items = payload.get("data") or []
        if not items:
            raise RuntimeError(f"topic create returned empty data for title={title!r}")
        topic = items[0]
        if "id" not in topic:
            raise RuntimeError(f"topic create returned invalid item for title={title!r}: {topic}")
        return topic

    if "已存在" in str(payload.get("msg", "")):
        search_payload = request_json(base_url, token, f"/api/Topic/search?keywords={urllib.parse.quote(title)}")
        for item in search_payload.get("data") or []:
            if str(item.get("title", "")).strip() == title:
                return item

    raise RuntimeError(f"/api/Topic/create failed: {payload}")


def post_question(base_url: str, token: str, theme: str, category_id: int, topic_ids: List[int], access_key: str) -> int:
    title = f"{theme}怎么选，先看哪些维度？"
    detail = (
        "<p>准备搭一个{theme}，先把选型思路理清楚。</p>"
        "<ul>"
        "<li>如果以实时大屏为主，应该优先看哪些能力？</li>"
        "<li>如果要同时支持分析报表和权限控制，哪些功能不能缺？</li>"
        "<li>如果预算有限，开源方案和商用方案分别该怎么看？</li>"
        "</ul>"
        "<p>我更想要一套可以落地的判断框架，而不是泛泛而谈的平台名单。</p>"
    ).format(theme=theme)

    data: Dict[str, Any] = {
        "title": title,
        "detail": detail,
        "category_id": category_id,
        "question_type": "normal",
        "is_anonymous": 0,
        "access_key": access_key,
    }
    for index, topic_id in enumerate(topic_ids):
        data[f"topics[{index}][id]"] = topic_id

    payload = request_json(base_url, token, "/api/Question/publish", data)
    question_id = int((payload.get("data") or {}).get("id", 0))
    if not question_id:
        raise RuntimeError(f"question publish returned invalid id: {payload}")
    return question_id


def post_answer(base_url: str, token: str, question_id: int, access_key: str) -> None:
    content = (
        "先看四个维度：场景、实时性、治理能力、预算。\n"
        "- 场景决定是报表、看板还是实时大屏。\n"
        "- 实时性决定是否要流式计算和低延迟刷新。\n"
        "- 治理能力决定权限、审计、血缘和多租户是否够用。\n"
        "- 预算决定先用开源方案验证，还是直接上商用平台。\n\n"
        "一句话：先把“谁看、看什么、多久刷新、谁维护”这四件事说清楚，再选平台。"
    )
    request_json(
        base_url,
        token,
        "/api/Question/save_answer",
        {
            "id": 0,
            "question_id": question_id,
            "content": content,
            "is_anonymous": 0,
            "access_key": access_key,
        },
    )


def post_article(
    base_url: str,
    token: str,
    theme: str,
    category_id: int,
    topic_ids: List[int],
    cover: str,
    article_type: str,
    access_key: str,
) -> Dict[str, Any]:
    title = f"{theme}的选择：先定场景，再看实时、治理与成本"
    message = (
        "<p>大数据展示平台选型，最容易犯的错是只看界面，不看数据链路。</p>"
        "<p>更稳妥的做法，是先把场景拆开：是经营看板、业务大屏，还是面向分析人员的多维报表。不同场景对刷新频率、权限治理和接入成本的要求完全不同。</p>"
        "<ul>"
        "<li>第一步：确认展示目标，是实时监控、管理驾驶舱，还是分析型报表。</li>"
        "<li>第二步：确认数据来源，是数据库、消息队列、日志，还是多系统汇总。</li>"
        "<li>第三步：确认治理要求，是否需要权限隔离、审计和字段级控制。</li>"
        "<li>第四步：确认部署预算，是先用开源方案验证，还是直接上商用平台。</li>"
        "</ul>"
        "<p>如果把平台选型压缩成一句话：先看场景，再看数据，再看治理，最后才看界面。</p>"
    )

    data: Dict[str, Any] = {
        "title": title,
        "message": message,
        "category_id": category_id,
        "article_type": article_type,
        "cover": cover,
        "access_key": access_key,
    }
    for index, topic_id in enumerate(topic_ids):
        data[f"topics[{index}][id]"] = topic_id

    payload = request_payload(base_url, token, "/api/Article/publish", data)
    if payload.get("code") == 1:
        if (payload.get("data") or {}).get("status") == "pending_review":
            return {
                "status": "pending_review",
                "id": int((payload.get("data") or {}).get("id", 0)),
                "payload": payload,
            }
        article_id = int((payload.get("data") or {}).get("id", 0))
        if not article_id:
            raise RuntimeError(f"article publish returned invalid id: {payload}")
        return {"status": "published", "id": article_id, "payload": payload}

    if "等待管理员审核" in str(payload.get("msg", "")):
        return {
            "status": "pending_review",
            "id": int((payload.get("data") or {}).get("id", 0)),
            "payload": payload,
        }

    raise RuntimeError(f"article publish failed: {payload}")


def main() -> int:
    parser = argparse.ArgumentParser(description="Publish a complete FAQ + article chain.")
    parser.add_argument("--base-url", default=os.environ.get("BASE_URL", "https://www.frelink.top"))
    parser.add_argument("--api-token", default=os.environ.get("API_TOKEN", ""))
    parser.add_argument("--theme", default=os.environ.get("THEME", "大数据展示平台"))
    parser.add_argument("--question-category-id", type=int, default=int(os.environ.get("QUESTION_CATEGORY_ID", "20")))
    parser.add_argument("--article-category-id", type=int, default=int(os.environ.get("ARTICLE_CATEGORY_ID", "5")))
    parser.add_argument("--cover", default=os.environ.get("COVER_URL", "/static/generated/big-data-platform-cover.png"))
    parser.add_argument("--article-type", default=os.environ.get("ARTICLE_TYPE", "tutorial"))
    parser.add_argument(
        "--extra-topic",
        action="append",
        default=[],
        help="Additional topic titles to create and bind. Can be used multiple times.",
    )
    args = parser.parse_args()

    if not args.api_token:
        print("API_TOKEN is required.", file=sys.stderr)
        return 2

    theme = args.theme.strip()
    topic_titles = [theme, "数据可视化", "平台选型", "实时大屏"] + [t.strip() for t in args.extra_topic if t.strip()]
    topic_ids: List[int] = []
    created_topics: List[Dict[str, Any]] = []
    for title in topic_titles:
        topic = ensure_topic(args.base_url, args.api_token, title)
        topic_id = int(topic.get("id", 0))
        if topic_id:
            topic_ids.append(topic_id)
        created_topics.append({
            "id": topic_id,
            "title": title,
            "status": topic.get("status", "published"),
            "approval_id": int(topic.get("approval_id", 0)),
        })

    ready_topic_ids = [topic_id for topic_id in topic_ids if topic_id]
    if not ready_topic_ids:
        raise RuntimeError("no approved topic id available for downstream publish")

    hot_questions = request_json(args.base_url, args.api_token, "/api/Question/index?sort=hot&page=1&page_size=5")
    hot_articles = request_json(args.base_url, args.api_token, "/api/Article/index?sort=hot&page=1&page_size=5")

    stamp = time.strftime("%Y%m%d%H%M%S")
    slug = make_slug(theme)
    question_id = post_question(
        args.base_url,
        args.api_token,
        theme,
        args.question_category_id,
        ready_topic_ids,
        f"publish-chain-question-{slug}-{stamp}",
    )
    post_answer(
        args.base_url,
        args.api_token,
        question_id,
        f"publish-chain-answer-{slug}-{stamp}",
    )
    article_result = post_article(
        args.base_url,
        args.api_token,
        theme,
        args.article_category_id,
        ready_topic_ids,
        args.cover,
        args.article_type,
        f"publish-chain-article-{slug}-{stamp}",
    )
    article_id = int(article_result["id"])

    question_detail = request_json(args.base_url, args.api_token, f"/api/Question/detail?id={question_id}")
    answer_list = request_json(args.base_url, args.api_token, f"/api/Question/answers?question_id={question_id}&page=1&per_page=3")
    article_detail = request_json(args.base_url, args.api_token, f"/api/Article/detail?id={article_id}") if article_id else {"data": {}}

    summary = {
        "hot_question": (hot_questions.get("data") or [{}])[0].get("id"),
        "hot_article": (hot_articles.get("data") or [{}])[0].get("id"),
        "topics": created_topics,
        "question_id": question_id,
        "question_title": (question_detail.get("data") or {}).get("info", {}).get("title"),
        "answer_count": (question_detail.get("data") or {}).get("info", {}).get("answer_count"),
        "answer_preview": ((answer_list.get("data") or [{}])[0].get("content") if answer_list.get("data") else ""),
        "article_id": article_id,
        "article_status": article_result["status"],
        "article_message": article_result["payload"].get("msg"),
        "article_title": article_detail.get("data", {}).get("title"),
        "article_cover": article_detail.get("data", {}).get("cover"),
        "article_topics": [item.get("title") for item in article_detail.get("data", {}).get("topics", [])],
        "article_type": article_detail.get("data", {}).get("article_type"),
    }
    print(json.dumps(summary, ensure_ascii=False, indent=2))
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
